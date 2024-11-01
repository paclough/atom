<footer>

  <?php if (QubitAcl::check('userInterface', 'translate')) { ?>
    <?php echo get_component('sfTranslatePlugin', 'translate'); ?>
  <?php } ?>

  <?php echo get_component_slot('footer'); ?>

  <div id="print-date">
    <?php echo __('Printed: %d%', ['%d%' => date('Y-m-d')]); ?>
  </div>

  <div class="container">
        <div class="row">
            <div class="span2">
                <a href="http://www.miami.edu/" target="_blank" title="University of Miami">
                    <img src="../images/um-logo2.png" alt="University of Miami" class="footer-logo">
                </a>
            </div>
            <div class="span5">
                <h4><a href="https://www.library.miami.edu">University of Miami Libraries</a></h4>
                <p>1300 Memorial Drive | Coral Gables, FL 33124-0320 | <a href="tel:1-305-284-3233">(305) 284-3233</a></p>
            </div>
            <div class="span5">
                <div class="social">
                    <a href="https://www.facebook.com/pages/University-of-Miami-Libraries/16409329419"" target="_blank" title="Facebook"><i class="fa fa-facebook-square" aria-hidden="true"></i></a> <a href="https://twitter.com/UMiamiLibraries" target="_blank" title="Twitter"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> <a href="https://www.instagram.com/umiamilibraries/" target="_blank" title="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                </div>
                <div class="supplemental">
                    <p>&copy; <?php echo date("Y"); ?> | <a href="https://www.library.miami.edu/about/privacy-policy.html">Privacy</a> | <a href="mailto:webmaster.lib@miami.edu" target="_blank">Report Site Issue</a> | <a href="https://www.library.miami.edu/about/support-the-libraries.html">Make a Gift</a></p>
                    <img src="../images/fdlp-logo.png" alt="Federal Depository Library Program" class="fdlp-logo">
                </div>
            </div>
        </div>
  </div>

  <div id="js-i18n">
    <div id="read-more-less-links"
      data-read-more-text="<?php echo __('Read more'); ?>" 
      data-read-less-text="<?php echo __('Read less'); ?>">
    </div>
  </div>

</footer>
