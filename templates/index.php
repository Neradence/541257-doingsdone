<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/" class="tasks-switch__item">Повестка дня</a>
        <a href="/" class="tasks-switch__item">Завтра</a>
        <a href="/" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= (1 === $show_complete_tasks) ? "checked" : ""; ?> >
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php if (isset($do_list) && is_array($do_list)) : ?>
        <?php foreach ($do_list as $key) { ?>
            <?php if ((1 === $show_complete_tasks && isset($key['done']) && $key['done']) || (isset($key['done']) && ! $key['done'])) : ?>
                <?php /*потому что нет смысла окрашивать уже сделанные задачи, где done=true*/?>
                <?php if (isset($key['date']) && is_date_important($key['date']) && ! $key['done']) : ?>
                    <tr class="tasks__item task--important">
                <?php else : ?>
                    <tr class="tasks__item task<?= isset($key['done']) && $key['done'] ? " task--completed" : ""; ?>" >
                <?php endif; ?>
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <?php if (isset($key['name'], $key['done'])): ?>
                                <input class="checkbox__input visually-hidden" type="checkbox"<?= $key['done'] ? " checked" : ""; ?>>
                                <span class="checkbox__text"><?= isset($key['name']) ? htmlspecialchars($key['name']): ""; ?></span>
                            <?php endif; ?>
                        </label>
                    </td>
                        <td class="task__date"><?= isset($key['date']) ? htmlspecialchars($key['date']): ""; ?></td>
                    <td class="task__controls"></td>
                </tr>
            <?php endif; ?>
        <?php } ?>
    <?php endif; ?>
</table>