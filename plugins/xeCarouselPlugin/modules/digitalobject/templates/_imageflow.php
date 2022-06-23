<?php use_helper('Text') ?>

<?php if (isset($objects) && count($objects)> 0): ?>
<div class="jcarousel-container">

  <div class="jcarousel-wrapper">

    <div class="jcarousel-pdf">
      <a class="btn" href="<?php echo $pdf ?>" target="_blank">
      <i class="fa fa-file-pdf-o fa-lg"></i><?php echo " " . __("Link to pdf file") ?></a>
    </div>

    <div class="connected-carousels">
       <div class="stage">
        <div class="carousel carousel-stage">
          <ul id="lightgallery">
          <?php foreach ($objects as $item): ?>
            <li data-src="<?php echo $item['reference'] ?>" >
                <p>

                   <a href="<?php echo $item['reference'] ?>" >
                   <?php echo image_tag($item['thumbnail'],
                                          array('longdesc' => $item['reference'],
                                                'alt'      => $item['ruta']
                                      )); ?>
                  </a>
                </p>
            </li>
          <?php endforeach; ?>
          </ul>
        </div>
        
        <script type="text/javascript">
          var lightgallery = jQuery.noConflict();
          lightgallery(document).ready(function() {
          lightgallery ("#lightgallery").lightGallery();
          });
        </script>
        
        <a href="#" class="prev prev-stage"><span>&lsaquo;</span></a>
        <a href="#" class="next next-stage"><span>&rsaquo;</span></a>
      </div>

      <div class="navigation">
        <div class="carousel carousel-navigation">
          <ul>
            <?php foreach ($objects as $index => $item): ?>
              <li><a href="#"><?php echo $index + 1; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <a href="#" class="prev prev-navigation">&lsaquo;</a>
        <a href="#" class="next next-navigation">&rsaquo;</a>
      </div>

      <div class="carousel-counter">
          <span data-total="<?php echo count($objects); ?>">1 de <?php echo count($objects); ?></span>
      </div>
    </div>
  </div>

  <?php if (isset($limit) && $limit < $total): ?>
    <div class="result-count">
      <?php echo __('Results %1% to %2% of %3%', array('%1%' => 1, '%2%' => $limit, '%3%' => $total)) ?>
      <a href="<?php echo url_for(array('module' => 'digitalobject', 'action' => 'browse', 'slug' => $resource->slug)) ?>"><?php echo __('Show all') ?></a>
    </div>
  <?php endif ?>

</div>
<?php endif ?>
