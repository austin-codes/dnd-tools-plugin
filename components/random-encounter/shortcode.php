<?php


function fc_encounter_generator( $atts ) {
    global $territories;
    global $monsters;

    //dump($_POST);

    $encounter = NULL;
    if (isset($_POST['generate']) && $_POST['generate'] == "Random Encounter") {
        if ( isset($_POST['fc-ancounter-player-level']) && isset($_POST['fc-ancounter-area']) ) {
            $encounter = fc_generate_encounter($_POST['fc-ancounter-player-level'], $_POST['fc-ancounter-area']);
        }
    }

    ?>
<form class="fc-encounter-generator-form" action="" method="post">
    <label for="fc-ancounter-player-level">Group Level: </label>
    <select class="fc-encounter-generator-lvl" name="fc-ancounter-player-level">
        <?php
        for ($i=1;$i<21;$i++) {
            $sel = '';
            if (isset($_POST['fc-ancounter-player-level']) && $i == $_POST['fc-ancounter-player-level']) {
                $sel = ' selected="selected"';
            }
            ?>
            <option value="<?php echo $i; ?>" <?php echo $sel; ?>><?php echo $i; ?></option>
            <?php
        }
        ?>
    </select>
    <label for="fc-ancounter-area">Area of Travel:</label>
    <select class="fc-encounter-generator-area" name="fc-ancounter-area">
        <option value="all">Use All Monsters</option>
        <?php
        foreach ($territories->defaults() as $k => $v) {
            if ($k == 'mon_id') {
                continue;
            }
            $sel = '';
            if (isset($_POST['fc-ancounter-area']) && $k == $_POST['fc-ancounter-area']) {
                $sel = ' selected="selected"';
            }
            ?>
            <option value="<?php echo $k; ?>"<?php echo $sel; ?>><?php echo ucwords(str_replace("_", " ", $k)); ?></option>
            <?php
        }
        ?>
    </select>
    <br /><br />
    <input type="submit" name="generate" value="Random Encounter">
</form>
    <?php

    if (is_array($encounter)) {
        ?>
        <br/><br/>
        <h3>Calculated Rating: <?php echo $encounter['rating']['calc']; ?></h3>
        <?php

        //dump($encounter, "Encounter");
        foreach ($encounter['monsters'] as $mon) {
            ?>
            <div class="encounter-monster">
                <div class="count">
                    <?php echo $mon->count; ?>
                </div>
                <div class="name">
                    <?php echo $mon->name; ?>
                </div>
                <div class="size">
                    <?php echo $mon->size; ?>
                </div>
                <div class="alignment">
                    <?php echo $mon->alignment; ?>
                </div>
                <div class="type">
                    <?php echo $mon->type; ?>
                </div>
                <div class="challenge">
                    <?php echo $mon->challenge; ?>
                </div>
                <div class="source">
                    <?php echo $mon->source; ?>
                </div>
            </div>
            <?php
        }
        dump($encounter["rating"], "Rating");
    }
}

add_shortcode("fc-encounter-gen", 'fc_encounter_generator');
add_shortcode("fc_encounter_gen", 'fc_encounter_generator');





function fc_generate_encounter($lvl = 1, $area = 'all') {
    global $territories;

    $territories->select(array('mon_id'));

    if ($area != 'all') {
        $territories->where(array(
            array($area, 1),
        ));
    }

    $mon_list = $territories->get();

    $mon_array = array();

    foreach ($mon_list as $k=>$mon_obj) {
        $mon_array[] = $mon_obj->mon_id;
    }
    shuffle($mon_array);

    $monsters = array();

    $cr = 0;
    while ($cr < $lvl) {
        $mon = array_pop($mon_array);
        $single_mon = fc_retrieve_monster($mon, $lvl);
        if ( $single_mon->challenge < $lvl + 5 && $single_mon->challenge > $lvl - 7) {
            $monsters[] = $single_mon;
        }
        else {
            continue;
        }
        $rating = fc_retrieve_monsters_cr($monsters, $lvl);
        $cr = $rating['calc'];
    }

    $encounter = array(
        'rating' => $rating,
        'monsters' => $monsters,
    );

    return $encounter;

}




function fc_retrieve_monster($mon, $lvl) {
    global $monsters;
    $monster = $monsters->get($mon, TRUE);

    $count = 1;
    if ($monster->challenge < $lvl) {
        if ($monster->challenge == 0) {
            $count = rand(1, 12);
        }
        elseif ($monster->challenge < 1) {
            $count = rand(1, $lvl / $monster->challenge) * $monster->challenge;
            $count = floor($count);
        }
        else {
            $count = rand(1, $lvl / $monster->challenge);
            $count = floor($count);
        }
    }
    $monster->count = $count;

    return $monster;
}



function fc_retrieve_monsters_cr($mon_array, $lvl) {
    $cr = array();
    foreach ($mon_array as $mon) {
        for ($i=0; $i < $mon->count; $i++) {
            $cr[] = $mon->challenge;
        }
    }
    $multiplier = 1;
    if (count($cr) > 8) { $multiplier = 10; }
    else if (count($cr) > 5) { $multiplier = 5; }
    else if (count($cr) > 2) { $multiplier = 2; }
    $rating = array(
        'sum' => array_sum($cr),
        'count' => count($cr),
        'average' => array_sum($cr) / count($cr),
        'calc' => ($multiplier * array_sum($cr)),
        'array' => $cr
    );
    //$rating = array_sum($cr) * 0.5 * count($cr);
    return $rating;
}
