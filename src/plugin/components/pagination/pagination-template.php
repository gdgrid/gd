<?php

/* @var $this \gdgrid\gd\plugin\components\pagination\Pagination */

if (empty($this->pages['pages'])):

    return '';

endif;
?>
<ul class="pagination">
    <?php
    if ($this->marginControls):
        ?>
        <li title="<?= $this->controls[$this::FIRST] ?>"
            class="first <?= ($this->pages['controls'][$this::FIRST] === null ? 'disabled' : '') ?>">
            <?php
            if ($this->pages['controls'][$this::FIRST]):
                ?>
                <a href="<?= $this->pages['urlQuery'] . $this->pages['pageName'] . '=' . $this->pages['controls'][$this::FIRST] ?>"
                   data-page="<?= $this->pages['controls'][$this::FIRST] ?>">
                    <span><?= $this->controls[$this::FIRST] ?></span>
                </a>
                <?php
            else:
                ?>
                <span><?= $this->controls[$this::FIRST] ?></span>
                <?php
            endif;
            ?>
        </li>
        <?php
    endif;

    if ($this->directControls):
        ?>
        <li title="<?= $this->controls[$this::PREVIOUS] ?>"
            class="prev <?= ($this->pages['controls'][$this::PREVIOUS] === null ? 'disabled' : '') ?>">
            <?php
            if ($this->pages['controls'][$this::PREVIOUS]):

                $prev = $this->pages['controls'][$this::PREVIOUS];

                ?>
                <a href="<?= $this->pages['urlQuery'] . $this->pages['pageName'] . '=' . $prev ?>"
                   data-page="<?= $prev ?>">
                    <span>&laquo;</span>
                </a>
                <?php
            else:
                ?>
                <span>&laquo;</span>
                <?php
            endif;
            ?>
        </li>
        <?php
    endif;

    foreach ($this->pages['pages'] as $key => $page):
        ?>
        <li class="<?= $this->pages['linkPages'][$key] === null ? 'active' : '' ?>">
            <a href="<?= $this->pages['linkPages'][$key] ?: '#' ?>"><?= $page ?></a>
        </li>
        <?php
    endforeach;

    if ($this->directControls):

        ?>
        <li title="<?= $this->controls[$this::NEXT] ?>"
            class="next <?= ($this->pages['controls'][$this::NEXT] === null ? 'disabled' : '') ?>">
            <?php
            if ($this->pages['controls'][$this::NEXT]):

                $next = $this->pages['controls'][$this::NEXT];

                ?>
                <a href="<?= $this->pages['urlQuery'] . $this->pages['pageName'] . '=' . $next ?>"
                   data-page="<?= $next ?>">
                    <span>&raquo;</span>
                </a>
                <?php
            else:
                ?>
                <span>&raquo;</span>
                <?php
            endif;
            ?>
        </li>
        <?php
    endif;

    if ($this->marginControls):
        ?>
        <li title="<?= $this->controls[$this::LAST] ?>"
            class="last <?= ($this->pages['controls'][$this::LAST] === null ? 'disabled' : '') ?>">
            <?php
            if ($this->pages['controls'][$this::LAST]):
                ?>
                <a href="<?= $this->pages['urlQuery'] . $this->pages['pageName'] . '=' . $this->pages['controls'][$this::LAST] ?>"
                   data-page="<?= $this->pages['controls'][$this::LAST] ?>">
                    <span><?= $this->controls[$this::LAST] ?></span>
                </a>
                <?php
            else:
                ?>
                <span><?= $this->controls[$this::LAST] ?></span>
                <?php
            endif;
            ?>
        </li>
        <?php
    endif;
    ?>
</ul>