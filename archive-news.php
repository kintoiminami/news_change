<?php
global $wp;

// Get today's date
$today = date('Y/m/d');


//新着順のクエリ
$args = [
   'post_type'      => 'news',
   'orderby'        => 'date',
   'order'          => 'DESC',
   'posts_per_page' => -1,
];

//開催日・発売日順のクエリ
if(isset($_GET['order']) && $_GET['order']=='eventdate'){
   $args = [
      'post_type'      => 'news',
      'meta_key'       => 'eventdate',
      'orderby'        => 'meta_value',
      'order'          => 'ASC',
      'posts_per_page' => -1,
      'meta_query'     => [
         'key'     => 'eventdate',
         'value'   => $today,
         'compare' => '>',
         'type'    => 'DATE',
      ],
   ];
}



global $dp_options, $active_sidebar;
if (!$dp_options) $dp_options = get_design_plus_options();
if (null === $active_sidebar) $active_sidebar = get_active_sidebar();

get_header();
?>
<main class="l-main">
  <?php
  get_template_part('template-parts/page-header');
  if ($dp_options['show_breadcrumb_archive_news']) :
    get_template_part('template-parts/breadcrumb');
  endif;
  ?>
  <div class="l-mian__inner l-inner<?php if ($active_sidebar) echo ' l-2columns'; ?>">
    <!-- 過去のニュースへボタンを表示 -->
    <div class="p-item-carousel__filter p-item-archive__sub-categories p-cb__item-content ps">
      <ul class="p-item-archive__sub-categories__inner">
        <li class="p-item-carousel__filter-item p-item-archive__sub-categories__item">
          <a href="<?php echo get_template_directory_uri(); ?>/archive-news/">過去のニュース一覧</a>
        </li>
      </ul>
    </div>
    <div class="l-primary">
      <div class="p-news-archive__sort">
        <?php
        $current_url = home_url($wp->request);

        // 新着順のURLを生成
        $new_sort_url = add_query_arg(array(
          'order'          => 'news',
        ), $current_url);

        // 開催日順のURLを生成
        $date_sort_url = add_query_arg(array(
          'order'        => 'eventdate',
        ), $current_url);
        ?>

        <!-- ソートボタンを表示 -->
        <div>
          <ul class="styled_post_list_tabs">
            <li class="tab-label--1">
              <label for="styled_post_list_tab_widget-2-tab--1">
                <a href="<?=esc_url($new_sort_url); ?>">
                  新着順
                </a>
              </label>
            </li>
            <li class="tab-label--1">
              <label for="styled_post_list_tab_widget-2-tab--2">
                <a href="<?= esc_url($date_sort_url); ?>">
                  開催日・発売日が近い順
                </a>
              </label>
            </li>
          </ul>
        </div>
      </div>

      <!-- 開催日・発売日順の時 -->
      <div class="p-news-archive">
        <?php
        $wp_query = new WP_Query($args);
        while ($wp_query->have_posts()) :
          $wp_query->the_post();
          $image_url = null;
          if ($dp_options['show_thumbnail_archive_news']) :
            if (has_post_thumbnail()) :
              $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'size1');
              if ($image) :
                $image_url = $image[0];
              endif;
            endif;
            if (!$image_url) :
              $image_url = get_template_directory_uri() . '/img/no-image-300x300.gif';
            endif;
          endif;
        ?>
          <article class="p-news-archive__item">
            <a class="p-hover-effect--<?php echo esc_attr($dp_options['hover_type']); ?>" href="<?php the_permalink(); ?>">
              <?php
              if ($image_url) :
              ?>
                <div class="p-news-archive__item-thumbnail p-hover-effect__bg">
                  <div class="p-news-archive__item-thumbnail__image p-hover-effect__image" style="background-image: url(<?php echo esc_attr($image_url); ?>);"></div>
                </div>
              <?php
              endif;
              ?>
              <div class="p-news-archive__item-info">
                <?php
                if ($dp_options['show_date_news']) :
                ?>
                  <time class="text-left p-news-archive__item-date p-article__meta p-article__date" datetime="<?php the_time('c'); ?>">投稿日 <?php the_time('Y/m/d'); ?></time>
                  <time class="text-left p-news-archive__item-date p-article__meta p-article__date top-eventdate" datetime="<?php the_time('c'); ?>">開催・発売日 <?php the_field('eventdate'); ?>〜</time>
                <?php
                endif;
                ?>
                <h2 class="p-news-archive__item-title p-article__title js-multiline-ellipsis"><?php echo mb_strimwidth(strip_tags(get_the_title()), 0, 200, '...'); ?></2>
              </div>
            </a>
          </article>
        <?php
        endwhile;
        ?>
      </div>
      <?php
      get_template_part('template-parts/pager');
      ?>
    </div>
    <?php
    if ($active_sidebar) :
      get_sidebar();
    endif;
    ?>
  </div>
</main>
<?php
get_footer();
