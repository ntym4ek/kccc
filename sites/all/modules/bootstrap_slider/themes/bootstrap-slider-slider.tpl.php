<?php if ($slides) { 
$effect = check_plain(variable_get('bb_slideshow_effect', 'slide'));
$effect_class = ($effect=='fade') ? 'carousel-fade' : '' ; 
$numberOfSlides=0; foreach ($slides as $i => $slide) { $numberOfSlides++; } ?>
	<!-- #bootstrap-slider -->
	<div id="bootstrap-slider" class="carousel slide <?php print $effect_class; ?>">
	    
	    <?php if ($numberOfSlides>1) { ?>
			<!-- Indicators -->
			<ol class="carousel-indicators">
			<?php foreach($slides as $i => $slide) { 
			$active_indicator = ($i==0) ? 'active' : '' ; ?>
			<li data-target="#bootstrap-slider" data-slide-to="<?php print $i; ?>" class="<?php print $active_indicator;?>"></li>
			<?php } ?>
			</ol>
		<?php } ?>

		<!-- Wrapper for slides -->
        <div class="carousel-inner">

		<?php foreach($slides as $i => $slide) {  
			$active_slide = ($i==0) ? 'active' : '' ;
			$file_uri = file_create_url($slide['image_path']); ?>
			
			<!-- slider-item-<?php print $i; ?> -->
			<div class="item <?php print $active_slide; ?>">
	            <a href="<?php print base_path().$slide['image_url'];?>"><img src="<?php print $file_uri;?>" class="img-responsive" alt="" /></a>
	            <?php if ($slide['image_title'] || $slide['image_description'] ) { ?>
	            <div class="carousel-caption">
	                <h2><?php print $slide['image_title'];?></h2>
	                <p><?php print $slide['image_description'];?></p>
	            </div>
	            <?php } ?>
            </div>
            <!-- EOF: slider-item -->

		<?php } ?> 

		</div>

	    <?php if ($numberOfSlides>1) { ?>        
        <!-- Controls -->
        <a class="left carousel-control" href="#bootstrap-slider" data-slide="prev">
        <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#bootstrap-slider" data-slide="next">
        <span class="icon-next"></span>
        </a>
		<?php } ?>

	</div>
	<!-- EOF:#bootstrap-slider -->
<?php } 
 else {
 	$no_slides = t('No slides found. Please go to <a href="@bb_url">Home » Administration » Structure » Bootstrap Slider</a> add some slides and check the <strong>published</strong> option.', array('@bb_url' => url('admin/structure/bootstrap-slider')));
 	print $no_slides;
 }?>