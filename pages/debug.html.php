
<?php
    # Debug PHP
    $_MSG["title"] = ["Test", "Lorem ipsum, come al solito, dolor sit amet"];
    dump(array_reverse(explode(".", $_SERVER["HTTP_HOST"])));
?>

<!-- Debug HTML -->
<main <?= £::put([1,2,3]) ?>>
    <section class="pt-10">
        <div class="container">
            <!-- Celle -->
            <div class="row mb-5">
                <!-- Cella Singola -->
                <div class="col-lg-4 mb-5">
                    <a class="card card-link border-top border-top-lg border-primary lift text-center o-visible h-100" href="#!"
                        ><div class="card-body">
                            <div class="icon-stack icon-stack-xl bg-primary-soft text-primary mb-4 mt-n5 z-1 shadow"><i class="fad fa-user"></i></div>
                            <h5>Sales</h5>
                            <p class="card-text">Ready to open an account? Have questions about purchasing a product?</p>
                        </div>
                        <div class="card-footer">
                            <div class="text-primary font-weight-bold d-inline-flex align-items-center">Contact Sales<i class="fas fa-arrow-right text-xs ml-1"></i></div></div
                    ></a>
                </div>
                <div class="col-lg-4 mb-5">
                    <a class="card card-link border-top border-top-lg border-secondary lift text-center o-visible h-100" href="#!"
                        ><div class="card-body">
                            <div class="icon-stack icon-stack-xl bg-secondary-soft text-secondary mb-4 mt-n5 z-1 shadow"><i class="fad fa-life-ring"></i></div>
                            <h5>Support</h5>
                            <p class="card-text">Need help with a product that you just purchased? Need help with your account?</p>
                        </div>
                        <div class="card-footer">
                            <div class="text-secondary font-weight-bold d-inline-flex align-items-center">Contact Support<i class="fas fa-arrow-right text-xs ml-1"></i></div></div
                    ></a>
                </div>
                <div class="col-lg-4 mb-5">
                    <a class="card card-link border-top border-top-lg border-teal lift text-center o-visible h-100" href="#!"
                        ><div class="card-body">
                            <div class="icon-stack icon-stack-xl bg-teal-soft text-teal mb-4 mt-n5 z-1 shadow"><i class="fad fa-tv"></i></div>
                            <h5>Media</h5>
                            <p class="card-text">Looking to contact our media team for a press release or related story?</p>
                        </div>
                        <div class="card-footer">
                            <div class="text-teal font-weight-bold d-inline-flex align-items-center">Contact Media<i class="fas fa-arrow-right text-xs ml-1"></i></div></div
                    ></a>
                </div>
            </div>
            <!-- Due Scritte -->
            <div class="row justify-content-center text-center">
                <!-- Scritta Singola -->
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <h5>Join us on Discord!</h5>
                    <p class="font-weight-light mb-0">Join the discussion on Discord. Our community can help answer questions!</p>
                </div>
                <div class="col-lg-5">
                    <h5>General Support</h5>
                    <p class="font-weight-light mb-0">For any other support questions, please send us an email at <a href="#!">support@example.com</a></p>
                </div>
            </div>
            <hr class="my-10" /> <!-- Divisione -->
            <!-- Scritta Singola -->
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2>Can't find the answer you need?</h2>
                    <p class="lead mb-5">Contact us and we'll get back to you as soon as possible with a solution to whatever issues you're having with SB UI Kit Pro.</p>
                </div>
            </div>
            <!-- Tre Opzioni -->
            <div class="row align-items-center mb-10">
                <!-- Opzione Singola -->
                <div class="col-lg-4 text-center mb-5 mb-lg-0">
                    <div class="section-preheading">Message Us</div>
                    <a href="#!">Start a chat!</a>
                </div>
                <div class="col-lg-4 text-center mb-5 mb-lg-0">
                    <div class="section-preheading">Call Anytime</div>
                    <a href="#!">(555) 565-1846</a>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="section-preheading">Email Us</div>
                    <a href="#!">support@startbootstrap.com</a>
                </div>
            </div>
            <form>
                <div class="form-row">
                    <div class="form-group col-md-6"><label class="text-dark" for="inputName">Full name</label><input class="form-control py-4" id="inputName" type="text" placeholder="Full name" /></div>
                    <div class="form-group col-md-6"><label class="text-dark" for="inputEmail">Email</label><input class="form-control py-4" id="inputEmail" type="email" placeholder="name@example.com" /></div>
                </div>
                <div class="form-group"><label class="text-dark" for="inputMessage">Message</label><textarea class="form-control py-3" id="inputMessage" type="text" placeholder="Enter your message..." rows="4"></textarea></div>
                <div class="text-center"><button class="btn btn-primary btn-marketing mt-4" type="submit">Submit Request</button></div>
            </form>
        </div>
    </section>
</main>