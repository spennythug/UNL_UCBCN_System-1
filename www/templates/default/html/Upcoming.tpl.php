<div class="wdn-grid-set">
    <section class="bp1-wdn-col-one-third">
        <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
    </section>
    <section id="updatecontent" class="day_cal bp1-wdn-col-two-thirds">
        <?php
        echo $savvy->render($context, 'hcalendar/Upcoming.tpl.php');
        ?>
    </section>
</div>
