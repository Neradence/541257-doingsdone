<div class="modal" <?php if ($hidden_auth) { echo ' hidden ' ; } ?>  id="user_login">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Вход на сайт</h2>

    <form class="form" method="post" enctype="multipart/form-data">
        <div class="form__row">

            <input type="hidden" value="login_user" name="form_type" />

            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <input class="form__input<?php if (isset($auth_form_state['email_err'])) { echo " form__input--error" ; } ?>" type="text" name="email" id="auth_email" value="<?php if (isset($auth_form_state['email'])) { echo htmlspecialchars($auth_form_state['email']); } ?>" placeholder="Введите e-mail">

            <?php if (isset($auth_form_state['email_err'])) { ?>
                <p class="form__message"><?=$auth_form_state['email_err'];?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>
            <input class="form__input<?php if (isset($auth_form_state['password_err'])) { echo " form__input--error" ; } ?>" type="password" name="password" id="auth_password" value="" placeholder="Введите пароль">

            <?php if (isset($auth_form_state['password_err'])) { ?>
                <p class="form__message"><?=$auth_form_state['password_err'];?></p>
            <?php } ?>
        </div>

        <div class="form__row form__row--controls">

            <?php if (isset($auth_form_state['form_err'])) { ?>
                <p class="error-message"><?=$auth_form_state['form_err'];?></p>
            <?php } ?>

            <input class="button" type="submit" name="" value="Войти">
        </div>
    </form>
</div>