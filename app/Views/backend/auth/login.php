<?= $this->include('/backend/template/meta'); ?>
<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a><b><?=lang('Auth.loginTitle')?></b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Ucapkan Basmalah Silahkan Login</p>
                <?= view('Myth\Auth\Views\_message_block') ?>
                <form action="<?= base_url("login") ?>" method="post">
                
                    <?php if ($config->validFields === ['email']): ?>
                        <div class="input-group mb-3">
							<input type="email" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
                                name="login" placeholder="<?=lang('Auth.email')?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                                <div class="invalid-feedback">
								    <?= session('errors.login') ?>
							    </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="input-group mb-3">
							<input type="text" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>"
								   name="login" placeholder="<?=lang('Auth.emailOrUsername')?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                                <div class="invalid-feedback">
								    <?= session('errors.login') ?>
							    </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="input-group mb-3">
						<input type="password" name="password" class="form-control  <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>" placeholder="<?=lang('Auth.password')?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <div class="invalid-feedback">
								<?= session('errors.password') ?>
						</div>
                    </div>
                    <div class="row">
                        <?php if ($config->allowRemembering): ?>
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="remember">
                                    <label for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block"><?=lang('Auth.loginAction')?></button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <?php if ($config->allowRegistration) : ?>
					<p><a href="<?= url_to('register') ?>"><?=lang('Auth.needAnAccount')?></a></p>
                <?php endif; ?>
                <?php if ($config->activeResetter): ?>
                                <p><a href="<?= url_to('forgot') ?>"><?=lang('Auth.forgotYourPassword')?></a></p>
                <?php endif; ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
<?= $this->include('/backend/template/js'); ?>