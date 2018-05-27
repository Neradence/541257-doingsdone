<div class="modal" <?php if (!isset($formstate_project['form_err'])) { echo ' hidden ' ; } ?> id="project_add">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление проекта</h2>

    <form class="form"  action="/index.php" method="post">
        <input type="hidden" value="add_project" name="form_type" />

        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input<?php if (isset($formstate_project['name_err'])) { echo " form__input--error" ; } ?>" type="text" name="name" id="project_name" value="<?php if (isset($formstate_project['name'])) { echo htmlspecialchars($formstate_project['name']); } ?>" placeholder="Введите название проекта">
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</div>