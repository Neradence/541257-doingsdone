<section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

    <a class="button button--transparent content__side-button" href="#">Войти</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Регистрация аккаунта</h2>

    <form class="form" action="/index.php" method="post" enctype="multipart/form-data">
        <input type="hidden" value="add_user" name="form_type" />

        <div class="form__row form__row--controls">
            <?php if (isset($formstate['name_err'])) { ?>
                <p class="error-message"><?=$formstate['name_err'];?></p>
            <?php } ?>

        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>

            <input class="form__input<?php if (isset($formstate['email_err'])) { echo " form__input--error" ; } ?>" type="text" name="email" id="email" value="<?php if (isset($formstate['email'])) { echo htmlspecialchars($formstate['email']); } ?>" placeholder="Введите e-mail">

            <?php if (isset($formstate['email_err'])) { ?>
                <p class="form__message"><?=$formstate['email_err'];?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input<?php if (isset($formstate['password_err'])) { echo " form__input--error" ; } ?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">

            <?php if (isset($formstate['password_err'])) { ?>
                <p class="form__message"><?=$formstate['password_err'];?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="name">Имя <sup>*</sup></label>

            <input class="form__input<?php if (isset($formstate['name_err'])) { echo " form__input--error" ; } ?>" type="text" name="name" id="name" value="<?php if (isset($formstate['name'])) { echo htmlspecialchars($formstate['name']); } ?>" placeholder="Введите имя">
        </div>

            <input class="button" type="submit" name="" value="Зарегистрироваться">
        </div>
    </form>
</main>