<?php
/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
$doit = mysqli_real_escape_string($db, $_GET['doit']);
$clan = trim(mysqli_real_escape_string($db, $_POST['clan']));
$flagStyle = mysqli_real_escape_string($db, $_POST['style']);
$flagColor = mysqli_real_escape_string($db, $_POST['color']);
$flagSigil = mysqli_real_escape_string($db, $_POST['sigil']);
$declared = mysqli_real_escape_string($db, $_POST['declared']);
$founded = mysqli_real_escape_string($db, $_POST['found']);
$id = $char['id'];
$message = "Form a new Clan";

$wikilink = "Clan";

// MAKE CLAN

if ($doit == 1) {

    // CHECK IF CLAN EXISTS ALREADY
    $query = "SELECT * FROM Soc WHERE name='$clan'";
    $resultb = mysqli_query($db, $query);

    // IF CLAN DOESNT EXIST
    if (!mysqli_fetch_assoc($resultb) && strlen($clan) <= 30 && !preg_match("/[^a-z ]+/i", $clan) && strlen($clan) > 2 && strtolower($clan) != strtolower("No One") && $flagStyle != "0" && $flagSigil != "0") {
        // SET DATABASE TABLES
        $ally = array('0');
        $ally[str_replace(" ", "_", $clan)] = 1;
        $array = serialize($array);
        $ally = serialize($ally);
        $flag = "Flag" . $flagStyle . "-" . $flagColor . ".gif";
        $sigil = $flagSigil . ".gif";
        $area_score = serialize(array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'));
        $area_rep = array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
        $area_rep[$founded] = 100;
        $area_reps = serialize($area_rep);
        $upgrades = serialize(array('0', '0', '0', '0', '0'));
        $office = array(0 => array('0'));
        $office[$founded][0] = '1';
        $offices = serialize($office);
        $querya = "INSERT INTO Soc (name,   leader, leaderlast, about,private_info,invite,members,allow,stance, blocked,score,area_score,   area_rep,    bank,inactivity,flag,   sigil,   upgrades,   offices,   align,declared) 
                        VALUES ('$clan','$name','$lastname','',   '',          '0',   '1',    '0',  '$ally','',     '0',  '$area_score','$area_reps','0', '5',       '$flag','$sigil','$upgrades','$offices',0,    '$declared')";
        $result = mysqli_query($db, $querya);

		
        $query = "SELECT * FROM Soc WHERE name='$clan'";
        $result = mysqli_query($db, $query);
        $society = mysqli_fetch_array($result);
        $querya = "INSERT INTO messages (id, checktime, message) VALUES ('$society[id]','0','a:0:{}')";
        $result = mysqli_query($db, $querya);
        $querya = "UPDATE Users SET society='$clan', soc_rank='1' WHERE id='$id'";
        $result = mysqli_query($db, $querya);
        $ustats = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Users_stats WHERE id='$id'"));
        $ustats['clans_joined']++;
        mysqli_query($db, "UPDATE Users_stats SET clans_joined='$ustats[clans_joined]' WHERE id='$id' ");
        header("Location: $server_name/clan.php?time=$curtime");
        exit;
    }
}

if ($doit && strtolower($clan) == strtolower("No One"))
    $message = "You're pretty smart thinking of that. Too bad I thought of it first.";
elseif ($doit && (strlen($clan) > 30 || preg_match("/[^a-z ]+/i", $clan) || strlen($clan) < 2))
    $message = "Problem with Clan Name";
elseif ($doit && ($flagStyle == "0" || $flagSigil == "0"))
    $message = "You must select both a flag and sigil";
elseif ($doit)
    $message = "Clan already exists";

include('header.htm');

?>
<div class="row solid-back">
    <br/>
    <?php
    // MAKE CLAN SCREEN
    if ($char['society'] == '') {
        ?>
        <!-- CLAN FORM -->
        <form method="post" action="makeclan.php?doit=1">
            <div class="col-sm-6">
                <div class="form-group form-group-sm">
                    <label for="clan">Clan Name:</label>
                    <input type="text" name="clan" id="clan" class="form-control gos-form" maxlength="30"/>
                </div>
                <div class="form-group form-group-sm">
                    <label for="declared">Declare Alignment:</label>
                    <select id="declared" name='declared' class="form-control gos-form">
                        <option value="0">Neutral</option>
                        <option value="1">Light</option>
                        <option value="-1">Shadow</option>
                    </select>
                </div>
                <div class="form-group form-group-sm">
                    <label for="found">Founding City:</label>
                    <select id="found" name='found' class="form-control gos-form">
                        <option value="0">-Select-</option>
                        <?php
                        $result = mysqli_query($db, "SELECT id, name FROM Locations ORDER BY name");
                        while ($city = mysqli_fetch_array($result)) {
                            echo "<option value='$city[id]'>$city[name]</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group form-group-sm">
                    <label for="style">Clan Flag:</label>
                    <select id="style" name='style' class="form-control gos-form" onChange="javascript:setColors();">
                        <option value="0">-Select-</option>
                        <?php
                        for ($i = 1; $i < 10; $i++) {
                            echo "<option value='$i'>Style $i</option>";
                        }
                        ?>
                    </select>
                    <select id="color" name='color' class="form-control gos-form"
                            onChange="javascript:setFlag();"></select>
                </div>
                <div class="form-group form-group-sm">
                    <label for="sigil">Sigil:</label>
                    <select id="sigil" name='sigil' class="form-control gos-form" onChange="javascript:setSigil();">
                        <option value="0">-Select-</option>
                        <?php
                        $sigilList = array(
                            "bear", "claws", "cup", "deer", "dragon", "fire_magic", "hand", "lotus", "moon", "phoenix", "stone", "sun", "sun_2", "sword", "wolf","paw" , "paw_2" , "rose" , "golden_rose" , 
                            "skull" , "skull_2" , "soldier" , "soldier_2" , "spears" , "spears_2" , "sunburst" , "sunburst_2" , "sunburst_2" , "sunburst_3" , "tar_valon" , "tower" , "tower_2" , "warder" , 
                            "warder_2" , "eye" , "eye_2" , "anvil" , "anvil_2" , "archer" , "archer_2" , "badger" , "badger_2" , "coramoor" , "coramoor_2" , "dragon_sigil" , "dragon_sigil_2" , "dragons_fang" , 
                            "falcon" , "falcon_2" , "flying_dragon" , "flying_dragon_2" , "kraken" , "kraken_2" , "lions" , "lions_2" , "one_power" , "oros", "axe" , "axe_2" , "fort" , "fort_2" , "hawk" , "hawk_2" , "hawk_flying" ,
                            "hawk_flying_2" , "ship" , "ship_2" , "temple" , "temple_2" ,
                            );
                        foreach ($sigilList as $sigil) {
                            echo "<option value='" . str_replace(' ', '', $sigil) . "'>" . $sigil . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class='col-sm-6'>
                <table height=189 width=160 class="table-clear">
                    <tr>
                        <td id='display'>&nbsp;</td>
                    </tr>
                </table>
                <input type="Submit" name="submit" value="Form Clan" class="btn btn-sm btn-success">
            </div>
        </form>
        <?php
    } else {
        ?>
        <center><p class='text-danger'>You must leave your current clan first before you can create a new one!</p>
        </center>
        <?php
    }
    ?>
</div>


<script type="text/javascript">
    function setColors() {
        <?php
        $colorArray = array(
            '1' => array('Black', 'Gray' , 'Blue', 'Brown', 'DkBlue', 'Cyan' , 'Orange', 'Lime', 'Magenta', 'OliveGreen', 'Purple', 'Red', 'Yellow' , 'White' , 'Pink'),
            '2' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
            '3' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
            '4' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
            '5' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
            '6' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
            '7' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
            '8' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
            '9' => array('black', 'gray' , 'blue', 'brown', 'dkblue', 'cyan' , 'orange', 'lime', 'magenta', 'olivegreen', 'purple', 'red', 'yellow' , 'white' , 'pink'),
        );
        echo 'var c1 = new Array("', join($colorArray[1], '","'), '");';
        echo 'var c2 = new Array("', join($colorArray[2], '","'), '");';
        echo 'var c3 = new Array("', join($colorArray[3], '","'), '");';
        echo 'var c4 = new Array("', join($colorArray[4], '","'), '");';
        echo 'var c5 = new Array("', join($colorArray[5], '","'), '");';
        echo 'var c6 = new Array("', join($colorArray[6], '","'), '");';
        echo 'var c7 = new Array("', join($colorArray[7], '","'), '");';
        echo 'var c8 = new Array("', join($colorArray[8], '","'), '");';
        echo 'var c9 = new Array("', join($colorArray[9], '","'), '");';

        ?>
        var selElem = document.getElementById('style');
        var index = selElem.selectedIndex;
        var newElem = document.getElementById('color');
        var tmp = '';
        var arr = '';
        newElem.options.length = 0;
        if (index == 1) arr = c1;
        else if (index == 2) arr = c2.slice();
        else if (index == 3) arr = c3.slice();
        else if (index == 4) arr = c4.slice();
        else if (index == 5) arr = c5.slice();
        else if (index == 6) arr = c6.slice();
        else if (index == 7) arr = c7.slice();
        else if (index == 8) arr = c8.slice();
        else if (index == 9) arr = c9.slice();

        for (var i = 0; i < arr.length; i++) {
            tmp = arr[i];
            newElem.options[newElem.options.length] = new Option(tmp, tmp);
        }
        setFlag();
    }

    function setFlag() {
        var style = document.getElementById('style').value;
        var color = document.getElementById('color').value;

        document.getElementById('display').style.backgroundImage = "url(images/Flags/Flag" + style + "-" + color + ".gif)";
    }

    function setSigil() {
        var sigil = document.getElementById('sigil').value;
        if (sigil != "0") document.getElementById('display').innerHTML = "<img src='images/Sigils/" + sigil + ".png' width=160 height=197/>";
        else document.getElementById('display').innerHTML = "&nbsp;";
    }
</script>
<?php
include('footer.htm');
?>

