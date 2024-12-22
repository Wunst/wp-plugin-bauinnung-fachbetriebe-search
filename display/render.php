<div <?php
  echo get_block_wrapper_attributes();
?> >
<ul>

<?php
require_once( fachb_PLUGDIR . "includes/db.php" );

foreach ( fachb_list() as $betrieb ) { ?>
  <li><?php
    echo $betrieb->name;
  ?></li>
<?php }

?>
</ul>
</div>

