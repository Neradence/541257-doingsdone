<div class="modal" <?php if (!isset($formstate_task['form_err'])) { echo ' hidden ' ; } ?> id="task_add">
    <button class="modal__close" type="button" name="button" href="/">Закрыть</button>

    <h2 class="modal__heading">Добавление задачи</h2>

    <form class="form"  action="/index.php" method="post" enctype="multipart/form-data">
        <input type="hidden" value="add_task" name="form_type" />

        <div class="form__row">
            <?php if (isset($formstate_task['name_err'])) { ?>
                <p class="p.form__message"><?=$formstate_task['name_err'];?></p>
            <?php } ?>

            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input<?php if (isset($formstate_task['name_err'])) { echo " form__input--error" ; } ?>" type="text" name="name" id="name" value="<?php if (isset($formstate_task['name'])) { echo htmlspecialchars($formstate_task['name']); } ?>" placeholder="Введите название">
        </div>

        <div class="form__row">
            <?php if (isset($formstate_task['project_err'])) { ?>
                <p class="p.form__message"><?=$formstate_task['project_err'];?></p>
            <?php } ?>

            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?php if (isset($formstate_task['project_err'])) { echo " form__input--error" ; } ?>" name="project" id="project">
                <?php if (isset($categories) && is_array($categories)) { ; ?>
                    <?php foreach ($categories as $category) { ?>
                        <?php
                            if (isset($category['id'], $formstate_task['project'])) {
                                $is_selected = ($category['id'] === intval($formstate_task['project'])) ? 'selected' : '';
                            }
                        ?>
                            <?php if (isset($category['id']) && $category['name'] !== 'Все') { ?>
                                <option value="<?php if (isset($category['id'])) { echo $category['id'] ; } ?>" <?php if (isset($is_selected)) { echo $is_selected; } ?>><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php } ?>
                        <?php } ?>
                <?php } ?>
            </select>
        </div>

        <div class="form__row">
            <?php if (isset($formstate_task['date_err'])) { ?>
                <p class="p.form__message"><?=$formstate_task['date_err'];?></p>
            <?php } ?>

            <label class="form__label" for="date">Срок выполнения</label>

            <input class="form__input form__input--date <?php if (isset($formstate_task['date_err'])) { echo " form__input--error" ; } ?>" type="text" name="date" id="date" value="<?php if (isset($formstate_task['date'])) { echo htmlspecialchars($formstate_task['date']); } ?>"
                   placeholder="Введите дату и время">
        </div>

        <div class="form__row">
            <label class="form__label" for="preview">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</div>