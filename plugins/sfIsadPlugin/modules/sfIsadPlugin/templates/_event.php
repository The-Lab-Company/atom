<?php $sf_response->addJavaScript('date', 'last'); ?>

<?php echo $form->renderGlobalErrors(); ?>

<div class="section">

  <h3><?php echo __('Date(s)'); ?> <span class="form-required" title="<?php echo __('This is a mandatory element.'); ?>">*</span></h3>

  <table class="table table-bordered multiRow">
    <thead>
      <tr>
        <th style="width: 25%">
          <?php echo __('Type'); ?>
        </th><th style="width: 30%">
          <?php echo __('Date'); ?>
        </th><th style="width: 20%">
          <?php echo __('Start'); ?>
        </th><th style="width: 20%">
          <?php echo __('End'); ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($form->getEmbeddedForms() as $i => $item) { ?>
        <?php echo $item->renderGlobalErrors(); ?>
        <tr class="date <?php echo 0 == ++$i % 2 ? 'even' : 'odd'; ?> related_obj_<?php echo $i; ?>">
          <td>
            <div class="animateNicely">
              <?php echo $item['type']->renderError(); ?>
              <?php echo $item['type']->render(); ?>
            </div>
          </td><td>
            <div class="animateNicely">
              <?php echo $item['date']->renderError(); ?>
              <?php echo $item['date']->render(); ?>
            </div>
          </td><td>
            <div class="animateNicely">
              <?php echo $item['startDate']->renderError(); ?>
              <?php echo $item['startDate']->render(); ?>
            </div>
          </td><td>
            <div class="animateNicely">
              <?php echo $item['endDate']->renderError(); ?>
              <?php echo $item['endDate']->render(); ?>
            </div>
          </td>
        </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5"><a href="#" class="multiRowAddButton"><?php echo __('Add new'); ?></a></td>
      </tr>
    </tfoot>
  </table>

  <?php if (isset($help)) { ?>
    <div class="description">
      <?php echo $help; ?>
    </div>
  <?php } ?>

</div>
