<?php
extract(get_query_var('settingsMenu'));
?>
<?php if ($field['type'] === 'group') : ?>
    <tr>
        <th class="pb-0">
            <label><?php echo $field['title']; ?></label>
        </th>
        <?php if ($field['desc']) : ?>
            <td>
                <p class="description"><?= $field['desc'] ?></p>
            </td>
        <?php endif; ?>
    </tr>
    <?php foreach ($field['fields'] as $key => $grpField) : ?>
        <?php unset($grpField['fields']); ?>
        <tr class="row-group-<?= $grpField['name'] ?>">
            <th class="text-right" style="font-weight: normal;">
                <?= $grpField['title'] ?>
            </th>
            <td class="pl-3 pb-3">
                <?php $menu->render_fields($grpField); ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr class="row-<?= $field['name'] ?>">
        <th>
            <label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
        </th>
        <td>
            <?php $menu->render_fields($field); ?>
        </td>
    </tr>
<?php endif; ?>