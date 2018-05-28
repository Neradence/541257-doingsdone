<div class="modal" <?php if (!isset($formstate['_err']) || (isset($formstate['show']) && !$formstate['show'])) { echo ' hidden ' ; } ?>  id="user_login">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Вход на сайт</h2>

    <form class="form" method="post" enctype="multipart/form-data">
        <div class="form__row">

            <input type="hidden" value="login_user" name="form_type" />

            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <input class="form__input<?php if (isset($formstate['email_err'])) { echo " form__input--error" ; } ?>" type="text" name="email" id="auth_email" value="<?php if (isset($formstate['email'])) { echo htmlspecialchars($formstate['email']); } ?>" placeholder="Введите e-mail">

            <?php if (isset($formstate['email_err'])) { ?>
                <p class="form__message"><?=$formstate['email_err'];?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>
            <input class="form__input<?php if (isset($formstate['password_err'])) { echo " form__input--error" ; } ?>" type="password" name="password" id="auth_password" value="" placeholder="Введите пароль">

            <?php if (isset($formstate['password_err'])) { ?>
                <p class="form__message"><?=$formstate['password_err'];?></p>
            <?php } ?>
        </div>

        <div class="form__row form__row--controls">

            <?php if (isset($formstate['form_err'])) { ?>
                <p class="error-message"><?=$formstate['form_err'];?></p>
            <?php } ?>

            <input class="button" type="submit" name="" value="Войти">
        </div>
    </form>
</div>