<?php


function fallen_crown_monster_sort_admin_page() {
    add_submenu_page(
        'fallen-crown', // Parent Slug
        'Sort Monsters', // Page Title
        'Sort Monsters', // Menu Title
        'manage_options', // Capability
        'fallen-crown-sort-monsters', // Page Slug
        'fc_sort_monster_page' // Callback Function
    );
}

add_action('admin_menu', 'fallen_crown_monster_sort_admin_page');

function fc_sort_monster_page() {
    global $monsters;

    $all_monsters = $monsters->get();

    if ( isset($_POST['save-sort-monsters']) && $_POST['save-sort-monsters'] == 'save-locations' ) {
        fc_sort_monsters_update();
    }

    ob_start();
    //dump($_POST);
    ?>
<h1>Sort Monsters</h1>
<form class="sort-monster-form" action="" method="post">
    <input type="hidden" name="save-sort-monsters" value="save-locations">
    <input type="submit" name="save" value="Save Settings">
    <table class="sort-monster-table">
        <thead>
            <tr><?php fc_render_sort_monster_panel_header(); ?></tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ( $all_monsters as $mon) {
                $count++;
                rc_render_sort_monster_single_row($mon);
                if ($count % 10 == 0) {
                    ?>
                    <tr class="header-row">
                        <?php fc_render_sort_monster_panel_header(); ?>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>

    </table>
</form>

    <?php



    $output = ob_get_clean();
    echo $output;
}




function fc_render_sort_monster_panel_header() {
    global $monsters;
    global $territories;

    ?>
        <th class="id">
            ID
        </th>
    <?php
    $m = $monsters->defaults();
    foreach ($m as $k => $v) {
        $exclude = array('size', 'tags', 'xp');
        if (in_array($k, $exclude)) { continue; }
        ?>
        <th class="<?php echo $k; ?>">
            <?php echo ucwords( str_replace('_', ' ', $k) ); ?>
        </th>
        <?php
    }

    $t = $territories->defaults();
    //dump($t);

    foreach ($t as $k => $v) {
        if ($k == 'mon_id') { continue; }
        ?>
        <th class="<?php echo $k; ?>">
            <?php echo ucwords( str_replace('_', ' ', $k) ); ?>
        </th>
        <?php
    }
}



function rc_render_sort_monster_single_row($mon) {
    global $territories;

    ?>
    <tr>
    <?php

    foreach ($mon as $k => $v) {
        $exclude = array('size', 'tags', 'xp');
        if (in_array($k, $exclude)) { continue; }
        ?>
        <td class="<?php echo $k; ?>">
            <?php echo $v; ?>
        </td>
        <?php
    }

    $t = $territories->defaults();

    $territories->select(array('*'));
    $territories->where(array(array(
        'mon_id',
        intval($mon->ID)
    )));
    $mon_data = $territories->get();

    if (!empty($mon_data)) {
        $mon_data = (array) $mon_data[0];
    }
    else {
        $mon_data = $territories->defaults();
        $mon_data['mon_id'] = $mon->ID;
    }

    foreach ($t as $key => $val) {
        if ($key == 'mon_id') { continue; }

        $checked = "";
        if (!isset($mon_data[$key])) {dump($mon_data, "What is this?"); dump($mon_data);}
        if ($mon_data[$key] == 1) {
            $checked = ' checked="checked"';
        }
        ?>
        <td class="<?php echo $key; ?>">
            <input type="checkbox" name="ID<?php echo $mon->ID; ?>[]" value="<?php echo $key; ?>" <?php echo $checked; ?>>
        </td>
        <?php
    }


    ?>
    </tr>
    <?php

}



function fc_sort_monsters_update() {
    global $territories;
    unset($_POST['save-sort-monsters']);
    unset($_POST['save']);

    foreach ($_POST as $id_key => $val_array) {
        $id = intval(str_replace('ID', '', $id_key));

        $data = $territories->defaults();
        $data["mon_id"] = $id;

        foreach ($val_array as $loc) {
            $data[$loc] = 1;
        }

        $territories->select('ID');
        $territories->where(array(
            array('mon_id', $id),
        ));

        $row_id = intval( $territories->get(NULL, TRUE) );
        $territories->reset();

        if ($data['none']) {
            $data = $territories->defaults();
            $data["mon_id"] = $id;
            $data["none"] = 1;
        }

        if ( $row_id == 0 ) {
            $territories->add($data);
        }
        else {
            $territories->update($row_id, $data);
        }
    }
}
