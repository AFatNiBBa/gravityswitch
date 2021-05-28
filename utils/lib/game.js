
class Manager {
	//| Semplificatore di Intanziazione di scena
    constructor()
    {
		this.loop = [];
		this.clock = new THREE.Clock();
        [this.scene, this.renderer, this.camera] = [
            new THREE.Scene(),
            new THREE.WebGLRenderer({ antialias: true, alpha: true }),
            new THREE.PerspectiveCamera(
                75,
                window.innerWidth / window.innerHeight,
                0.1,
                1000
            )
        ];

		window.addEventListener("resize", Manager.prepare(
			() => {
				this.renderer.setSize(window.innerWidth, window.innerHeight);
				this.camera.aspect = window.innerWidth / window.innerHeight;
				this.camera.updateProjectionMatrix();
			}
		));

        this.renderer.setClearColor("#e5e5e5");
		this.camera.position.z = 5;
		this.clock.start();
        
		Manager.prepare(self => {
			const delta = this.clock.getDelta();
			for (const e of this.loop)
				e?.(this, delta);
			this.renderer.render(this.scene, this.camera);
			requestAnimationFrame(self);
		});
    }

    apply(comp = document.querySelector("body"), full = true)
    {
		const { domElement: canvas } = this.renderer;
        comp.appendChild(canvas);
        if (full)
        {
            canvas.style.display = "block";
            comp.style.margin = "0px";
            comp.style.height = "100vh";
        }
        return this;
	}

	mouse()
	{
		this.orbit = new THREE.OrbitControls(this.camera, this.renderer.domElement);
		this.orbit.maxPolarAngle = Math.PI;
        this.orbit.enabled = true;
		this.loop.push(() => this.orbit.update());
		return this;
	}

	static prepare(f)
	{
		//| Esegue e restituisce una funzione
		const out = () => f(out);
		out();
		return out;
	}

	static Lock = class {
		//| Fa si che non vengano più animazioni con lo stesso "Lock" allo stesso tempo
		constructor(defaults = { delay: .3, easing: "linear" }) {
			this.defaults = defaults;
		}

		get completed() {
			return this.session?.completed ?? true;
		}

		wait() {
			return this.session?.promise;
		}

		change(p) {
			const out = this.session = { completed: false };
			if (p instanceof Promise)
				p.then(() => out.completed = true);
			return out.promise = p;
		}
	}

	static animation(targets, duration, opts, lock = new Manager.Lock())
	{
		//| Animazione
		const out = Object.assign({ targets, duration }, lock.defaults, opts);
		const prev = lock.wait();
		return lock.change(
			new Promise(async t => {
				await prev;
				t(await anime(out).finished);
			})
		);
	}
	
	static async sequence(seq)
	{
		//| Array di gruppi di animazioni, ogni gruppo va eseguito dopo il precedente, ma ogni animazione in un gruppo va eseguita allo stesso tempo
		for (const e of seq)
		{
			await Promise.all(
				e.map(x => 
					Manager.animation.apply(null, x)
				)
			);
		}
	}
}

class Physical extends Manager
{
	//| Manager con CANNON.js
	constructor()
	{
		super();
		this.planes = [];
		[this.world, this.step, this.damp] = [
			new CANNON.World(),
			1 / 60,
			.01
		];
		
		this.world.broadphase = new CANNON.NaiveBroadphase();
		this.world.gravity.set(0, -9.81, 0);
		this.loop.push(() => this.world.step(this.step));
	}

	body(mesh, mass = 5, shape)
	{
		this.remove(mesh);
		const { position, quaternion } = mesh;
		mesh.rigidbody = new CANNON.Body({
			mass,
			material: new CANNON.Material(),
			position: new CANNON.Vec3(position.x, position.y, position.z),
			quaternion: new CANNON.Quaternion(quaternion.x, quaternion.y, quaternion.z, quaternion.w)
		});
		mesh.rigidbody.linearDamping = this.damp;
		mesh.rigidbody.addShape(
			(shape instanceof CANNON.Shape)
			? shape
			: CANNON.Box.setFromObject(
				mesh,
				(shape instanceof Function)
				? shape
				: (x => x === mesh)
			)
		);
		this.world.add(mesh.rigidbody);
		mesh.rigidbody.loopUpdate = () => {
			const { position, quaternion } = mesh.rigidbody;
			mesh.position.set(position.x, position.y, position.z);
			mesh.quaternion.set(quaternion.x, quaternion.y, quaternion.z, quaternion.w);
		};
		this.loop.push(mesh.rigidbody.loopUpdate);
		return mesh;
	}

	remove(mesh)
	{
		const body = (mesh instanceof CANNON.Body) ? mesh : mesh.rigidbody;
		if (body)
		{
			this.loop.remove(x => x === body.loopUpdate);
			this.world.removeBody(body);
			delete mesh.rigidbody;
		}
		return this;
	}

	plane(pos, rot)
	{
		const out = new CANNON.Body({
			mass: 0,
			material: new CANNON.Material()
		}).addShape(new CANNON.Plane());
		out.quaternion.setFromAxisAngle(new CANNON.Vec3(1, 0, 0), -Math.PI / 2)	//| Di base è sul piano 'xy'
		if (pos)
		{
			out.position.x += pos[0] ?? 0;
			out.position.y += pos[1] ?? 0;
			out.position.z += pos[2] ?? 0;
		}
		if (rot)
		{
			const e = new CANNON.Vec3(0, 0, 0);
			out.quaternion.toEuler(e);
			out.quaternion.setFromEuler(
				e.x + (rot[0] ?? 0),
				e.y + (rot[1] ?? 0),
				e.z + (rot[2] ?? 0)
			);
		}
		this.planes.push(out);
		this.world.add(out);
		return this;
	}

	band(center, object)
	{
		this.loop.remove(x => x === this.gravityBand, Infinity);
		if (center && object)
		{
			this.gravityBand = () => {
				this.world.gravity.set(
					center.position.x - object.position.x,
					center.position.y - object.position.y,
					center.position.z - object.position.z
				);
			};
			this.loop.push(this.gravityBand);
		}
	}
}

class Texture
{
	//| Texture completamente modificabile come '<canvas>'
	constructor(width, height)
	{
		const canvas = this.canvas = document.createElement("canvas");
		const ctx = this.context = this.canvas.getContext("2d");

		canvas.width = width ?? 512;
		canvas.height = height ?? 512;
		ctx.fillStyle = "#ffffff";
	}

	clear()
	{
		const temp = this.context.fillStyle;
		this.context.fillStyle = "#ffffff";
		this.context.fillRect(0, 0, this.canvas.width, this.canvas.height);
		this.context.fillStyle = temp;
		return this;
	}

	centerText(text, { font = "300px Arial", color = "#ff0000", offX = 0, offY = 0 })
	{
		this.context.beginPath();
		this.context.font = font;
		this.context.fillStyle = color;
		this.context.textBaseline = 'middle';
		this.context.textAlign = 'center';
		this.context.fillText(text + "", this.canvas.width / 2 + offX, this.canvas.height / 2 + offY);
		this.context.stroke();
		return this;
	}

	texture(prec)
	{
		prec?.dispose?.();
		const out = new THREE.Texture(this.canvas);
		out.needsUpdate = true;
		return out;
	}
}

if (true)
{
	//| Extension Base
	Array.prototype.remove = function(pred, count = 1)
	{
		//| Rimozione dei primi "count" elementi che sono risultati idonei da "pred"; Se "pred" non è fornito è contato come vero
		for (let i = 0; i < this.length && count > 0; i++)
		{
			if (!pred || pred(this[i], i, this))
			{
				this.splice(i--, 1);
				count--;
			}
		}
		return this;
	}
}

if (true && window.CANNON)
{
	//| Extension CANNON
	CANNON.Box.setFromObject = function(mesh, pred)
	{
		//| Inizializzazione forma di CANNON da Mesh (Statico)
		var rot = mesh.rotation.clone()
		mesh.rotation.set(0, 0, 0)
		Temp = new THREE.Box3().setFromObject(mesh, pred)
		mesh.rotation.set(rot)
		return new CANNON.Box(new CANNON.Vec3((Temp.max.x - Temp.min.x) / 2, (Temp.max.y - Temp.min.y) / 2, (Temp.max.z - Temp.min.z) / 2))
	}
}

if (true && window.THREE)
{
	//| Extension THREE
	THREE.Object3D.prototype.traverse = function(callback, pred)
	{
		//| Implementazione condizionalità "Object3D::traverse()"
		if (!pred || pred(this)) callback(this);
		for (const e of this.children)
			e.traverse(callback, pred);
	}

	THREE.Box3.prototype.setFromObject = function(object, pred)
	{
		//| Implementazione condizionalità "Box3::setFromObject()"
		this.makeEmpty();
		return this.expandByObject(object, pred);
	}

	THREE.Box3.prototype.expandByObject = (function () {
		// Computes the world-axis-aligned bounding box of an object (including its children),
		// accounting for both the object's, and children's, world transforms
		var scope, i, l;
		var v1 = new THREE.Vector3();
		function traverse( node ) {
			var geometry = node.geometry;
			if ( geometry !== undefined ) {
				if ( geometry.isGeometry ) {
					var vertices = geometry.vertices;
					for ( i = 0, l = vertices.length; i < l; i ++ ) {
						v1.copy( vertices[ i ] );
						v1.applyMatrix4( node.matrixWorld );
						scope.expandByPoint( v1 );
					}
				} else if ( geometry.isBufferGeometry ) {
					var attribute = geometry.attributes.position;
					if ( attribute !== undefined ) {
						for ( i = 0, l = attribute.count; i < l; i ++ ) {
							v1.fromBufferAttribute( attribute, i ).applyMatrix4( node.matrixWorld );
							scope.expandByPoint( v1 );
						}
					}
				}
			}
		}
		return function expandByObject( object, pred ) {
			scope = this;
			object.updateMatrixWorld( true );
			object.traverse( traverse, pred );
			return this;
		};
	}());
}

if (true && window.THREE)
{
	//| Extension Method Mesh
	THREE.Mesh.prototype.edges = function(color = 0x000000)
	{
		//| Visualizza i bordi del Mesh
		this.add(
			new THREE.LineSegments(
				new THREE.EdgesGeometry(this.geometry),
				new THREE.LineBasicMaterial({ color })
			)
		);
		return this;
	}

	THREE.Mesh.prototype.getBox3 = function(pred = x => !x.nonTangible)
	{
		//| Crea un "Box3" a partire da una Mesh
		return new THREE.Box3().setFromObject(this, pred);
	}

	THREE.Mesh.prototype.isIn = function(second, pred)
	{
		//| Indica se 'this' è all'interno di "second"
		/*
			Condizione (&&): (
				Bi <= Ai &&
				Af <= Bf
			)
		*/
		return second.getBox3(pred).containsBox(this.getBox3(pred));
	}

	THREE.Mesh.prototype.intersects = function(second, pred)
	{
		//| Indica se 'this' si interseca con "second"
		/*
			Condizione (&&): (
				(
					Bi <= Af <= Bf ||
					Ai <= Bf <= Af
				) && !(
					Af == Bi ||
					Bf == Ai
				)
			)
		*/
		return second.getBox3(pred).intersectsBox(this.getBox3(pred));
	}

	/*
		touch => 
		!intersect && 
		Condizione (&&): (
			Af == Bi ||
			Bf == Ai
		) || (
			Bi <= Af <= Bf ||
			Ai <= Bf <= Af
		)
	*/
}