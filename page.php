<?php

get_header();


?>

<div class="container-fluid">
<di class="container">
	<?php
    if(have_posts()):
        while ( have_posts() ) : the_post(); ?>

<div class="card mt-5">
	<div class="card-body">
			<h2 class='card-title text-center'><?php the_title() ?></h2>

<?php
       

?>

</div>
</div>


<?php
    endwhile;
    endif;
?>

</div>
</div>
    <!--/.Content-->


<?php

get_footer();

?>