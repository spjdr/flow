<div class="email" style="font-size: 14pt; padding: 22px 20px; margin: 15px; background-color: #ffc146; border: 1px solid #deab14; color: #111; font-family: Garamond, Times, Times New Roman, serif; border-radius: 5px; line-height: 1.3em">
<h2 style="margin: 0 0 .7em 0">Hej</h2>

<p style="margin: 0 0 .7em 0">Vi skriver til dig fordi, <b><?php echo html::chars($master->name) ?></b> har inviteret dig til at deltage i planlægningen af <b><?php echo html::chars($flow->title) ?></b> på &ldquo;<b><?php echo html::anchor('','Flow',array('style'=>'color: #3d647b;')) ?></b>&rdquo;. </p>

<p style="margin: 0 0 .7em 0">Hvis du ikke allerede har en konto på &ldquo;Flow&rdquo;, kan du registrere dig ved at udfylde vores <?php echo html::anchor('register','registrerings-formular',array('style'=>'color: #3d647b;')) ?>.</p>

<p style="margin: 0 0 .7em 0">Du accepterer invitationen ved først at <?php echo html::anchor('login','logge ind',array('style'=>'color: #3d647b;')) ?> og dernæst besøge linket: </p>

<p class="link" style="margin: 0 0 .7em 15px"><?php echo html::anchor('join/'.$invitation_key,url::site('join/'.$invitation_key),array('style'=>'color: #3d647b;')) ?></p>

<p style="margin: 0 0 .7em 0">Hvis du ikke ønsker at deltage i forberedelserne til <?php echo html::chars($flow->title) ?>, behøver du ikke gøre andet end at vente. Vi sletter invitationen i løbet af to ugers tid, og du vil kun høre fra os igen, hvis andre folk skulle invitere dig til deres <i>flows</i>.</p> 

<p style="margin: 0 0 .7em 0">Med venlig hilsen,</p>
<p class="sender" style="margin: 0 0 2px 0; font-style: italic">Holdet bag Flow</p>
<p class="sender" style="margin: 0 0 2px 0; font-style: italic">Kim Lind Pedersen, <?php echo HTML::mailto('kim@spjdr.dk','kim@spjdr.dk',array('style'=>'color: #3d647b;')) ?></p>
<p class="sender" style="margin: 0 0 2px 0; font-style: italic">Nikolaj Josephsen, <?php echo HTML::mailto('nikolaj@spjdr.dk','nikolaj@spjdr.dk',array('style'=>'color: #3d647b;')) ?></p>
</div>