<?php

// Include parent styles in addition to child theme styles
// --------------------------------------------------------------------------------
function theme_enqueue_custom() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_script( 'child-scripts', get_stylesheet_directory_uri() . '/scripts.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_custom' );



// Custom theme actions, filters, shortcodes
// --------------------------------------------------------------------------------

// Utility - Remove P tags around images
// -------------------------------------
function filter_ptags_on_images($content){
	return preg_replace('/(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?/iU', '<p class="image">\1\2\3</a></p>', $content);
}
add_filter('the_content', 'filter_ptags_on_images');

// Shortcode - Divider
// -------------------
function divider_func( $atts ){
	return '<div class="divider"> </div>';
}
add_shortcode( 'divider', 'divider_func' );

// Feature - Add Pin It buttons to each linked image
// -------------------------------------------------
function mh_wrap_image( $content ) {
  global $post;
  $images_regex = '/<p class="image">(.+?)<\/p>/';

  // If we get any hits then put the code before the a tag
  if ( preg_match_all( $images_regex , $content, $image_matches ) ) {;
    for ( $count = 0; $count < count( $image_matches[0] ); $count++ )
    {
      // Old img tag
      $orig_image = $image_matches[0][$count];

      // Get the img URL, it's needed for the button code
      $image_src = preg_replace( '/.*src=\"(.*?)\".*?/' , '\1' , $orig_image );
      $image_src = preg_replace( '/(.+?) .*/' , '\1' , $image_src );

      // Put together the pinterest code to place before the img tag
      $mh_pinterest_code = '<span class="pinterest-button"><a href="http://pinterest.com/pin/create/button/?url=' . urlencode( get_permalink() ) . '&media=' . $image_src . '&description=' . get_the_title() . '" class="pin-it-post"></a></span>';

      // Replace before the img tag in the new string
      $new_image = preg_replace( '/<p class=\"image\">/' , '<p class="image">'.$mh_pinterest_code , $orig_image );

      // make the substitution
      $content = str_replace( $orig_image, $new_image , $content );
    }
  }
  return $content;
}
add_filter( 'the_content' , 'mh_wrap_image' );



// Attitude Theme Function Overrides
// --------------------------------------------------------------------------------

// Remove any actions that we are overriding below
// -----------------------------------------------
function remove_attitude_actions() {
	remove_action('attitude_footer','attitude_footer_info', 30);
	remove_action('attitude_footer','attitude_footer_info', 30);
}
add_action('init','remove_attitude_actions');

// Override default footer function
// --------------------------------
function sc_footer_info() {
	$output = '<div class="copyright">'.__( 'Copyright &copy; 2011 &ndash;', 'attitude' ).' '.attitude_the_year().' ' .attitude_site_link().' </div><!-- .copyright -->';
	echo $output;
}
add_action( 'attitude_footer', 'sc_footer_info', 30);

// Override default blog page function
// -----------------------------------
function attitude_theloop_for_template_blog_full_content() {
	global $post;

	global $wp_query, $paged;
	if( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );
	}
	elseif( get_query_var( 'page' ) ) {
		$paged = get_query_var( 'page' );
	}
	else {
		$paged = 1;
	}
	$blog_query = new WP_Query( array( 'post_type' => 'post', 'paged' => $paged ) );
	$temp_query = $wp_query;
	$wp_query = null;
	$wp_query = $blog_query; 

	global $more;    // Declare global $more (before the loop).

	if( $blog_query->have_posts() ) {
		while( $blog_query->have_posts() ) {
			$blog_query->the_post();

			do_action( 'attitude_before_post' );
			?>
			<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<article>

					<?php do_action( 'attitude_before_post_header' ); ?>

					<header class="entry-header">
						<p class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></p>
						<h2 class="entry-title">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
						</h2><!-- .entry-title -->
					</header>

					<?php do_action( 'attitude_after_post_header' ); ?>

					<?php do_action( 'attitude_before_post_content' ); ?>

					<div class="entry-content clearfix">
						<?php
    				$more = 0;       // Set (inside the loop) to display content above the more tag.

    				the_content( __( 'Read more', 'attitude' ) );

    				wp_link_pages( array( 
    					'before'            => '<div style="clear: both;"></div><div class="pagination clearfix">'.__( 'Pages:', 'attitude' ),
    					'after'             => '</div>',
    					'link_before'       => '<span>',
    					'link_after'        => '</span>',
    					'pagelink'          => '%',
    					'echo'              => 1 
    					) );
    					?>
    				</div>

    				<?php do_action( 'attitude_after_post_content' ); ?>

    				<?php do_action( 'attitude_before_post_meta' ); ?>

    				<div class="entry-meta-bar clearfix">
    					<div class="entry-meta">
    						<div class="share">
    							<h4>Share this post:</h4>
                  <ul class="share-buttons">
                    <li><div class="fb-share-button" data-href="<?php the_permalink(); ?>" data-layout="button"></div>
                      <div id="fb-root"></div>
                      <script>(function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=224037024321578";
                        fjs.parentNode.insertBefore(js, fjs);
                      }(document, 'script', 'facebook-jssdk'));</script>
                    </li>
                    <li><a href="https://twitter.com/share" class="twitter-share-button"{count} data-via="sweetcrafty"></a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></li>
                    <li><a data-pin-do="buttonPin" data-pin-color="red" href="https://www.pinterest.com/pin/create/button/?url=&media=&description="><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_20.png" /></a><script async defer src="//assets.pinterest.com/js/pinit.js"></script></li>
                  </ul>
                </div>

                <?php if ( comments_open() ) { ?>
                <div class="comments"><?php comments_popup_link( __( 'No Comments', 'attitude' ), __( '1 Comment', 'attitude' ), __( '% Comments', 'attitude' ), '', __( 'Comments Off', 'attitude' ) ); ?></div>
                <div class="clearfix"></div>
                <?php } ?>
                <?php if( has_category() ) { ?>
                <div class="category"><strong>Categories:</strong> <?php the_category(' // '); ?></div>
                <?php } ?>
              </div><!-- .entry-meta -->
            </div>

            <?php do_action( 'attitude_after_post_meta' ); ?>

          </article>
        </section>
        <?php
        do_action( 'attitude_after_post' );

      }
      if ( function_exists('wp_pagenavi' ) ) { 
        wp_pagenavi();
      }
      else {
        if ( $wp_query->max_num_pages > 1 ) {
         ?>
         <ul class="default-wp-page clearfix">
          <li class="previous"><?php next_posts_link( __( '&laquo; Previous', 'attitude' ), $wp_query->max_num_pages ); ?></li>
          <li class="next"><?php previous_posts_link( __( 'Next &raquo;', 'attitude' ), $wp_query->max_num_pages ); ?></li>
        </ul>
        <?php 
      }
    }
  }
  else {
   ?>
   <h2 class="entry-title"><?php _e( 'No Posts Found.', 'attitude' ); ?></h2>
   <?php
 }
 $wp_query = $temp_query;
 wp_reset_postdata();
}

// Override single blog post function
// -----------------------------------
function attitude_theloop_for_single() {
	global $post;

	if( have_posts() ) {
		while( have_posts() ) {
			the_post();

			do_action( 'attitude_before_post' );
			?>
			<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<article>

					<?php do_action( 'attitude_before_post_header' ); ?>

					<header class="entry-header">
						<p class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></p>
						<h2 class="entry-title">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
						</h2><!-- .entry-title -->
					</header>

					<?php do_action( 'attitude_after_post_header' ); ?>

					<?php do_action( 'attitude_before_post_content' ); ?>

					<div class="entry-content clearfix">
						<?php
	    				$more = 0;       // Set (inside the loop) to display content above the more tag.

	    				the_content( __( 'Read more', 'attitude' ) );

	    				wp_link_pages( array( 
	    					'before'            => '<div style="clear: both;"></div><div class="pagination clearfix">'.__( 'Pages:', 'attitude' ),
	    					'after'             => '</div>',
	    					'link_before'       => '<span>',
	    					'link_after'        => '</span>',
	    					'pagelink'          => '%',
	    					'echo'              => 1 
	    					) );
	    					?>
              </div>

              <?php do_action( 'attitude_before_post_meta' ); ?>

              <div class="entry-meta-bar clearfix">
               <div class="entry-meta">
                <div class="share">
                  <h4>Share this post:</h4>
                  <ul class="share-buttons">
                    <li><div class="fb-share-button" data-href="<?php the_permalink(); ?>" data-layout="button"></div>
                      <div id="fb-root"></div>
                      <script>(function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=224037024321578";
                        fjs.parentNode.insertBefore(js, fjs);
                      }(document, 'script', 'facebook-jssdk'));</script>
                    </li>
                    <li><a href="https://twitter.com/share" class="twitter-share-button"{count} data-via="sweetcrafty"></a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></li>
                    <li><a data-pin-do="buttonPin" data-pin-color="red" href="https://www.pinterest.com/pin/create/button/?url=&media=&description="><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_20.png" /></a><script async defer src="//assets.pinterest.com/js/pinit.js"></script></li>
                  </ul>                            </div>

                  <?php if ( comments_open() ) { ?>
                  <div class="comments"><?php comments_popup_link( __( 'No Comments', 'attitude' ), __( '1 Comment', 'attitude' ), __( '% Comments', 'attitude' ), '', __( 'Comments Off', 'attitude' ) ); ?></div>
                  <div class="clearfix"></div>
                  <?php } ?>
                  <?php if( has_category() ) { ?>
                  <div class="category"><strong>Categories:</strong> <?php the_category(' // '); ?></div>
                  <?php } ?>
                </div><!-- .entry-meta -->
              </div>

              <?php do_action( 'attitude_after_post_meta' ); ?>

            </article>
          </section>
          <?php

          do_action( 'attitude_after_post_content' );
          do_action( 'attitude_after_post' );

        }
      }
      else {
       ?>
       <h1 class="entry-title"><?php _e( 'No Posts Found.', 'attitude' ); ?></h1>
       <?php
     }
   }


   ?>