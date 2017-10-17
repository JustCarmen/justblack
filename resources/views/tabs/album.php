<?php use Fisharebest\Webtrees\Html; ?>

<div class="wt-album-tab row py-4">
	<?php foreach ($media_list as $media): ?>
		<figure class="figure text-center col-sm-6 col-md-4 col-lg-3 col-xl-2 mt-auto wt-album-tab-figure">
			<?= $media->displayImage(120, 120, 'contain', ['class' => 'img-thumbnail wt-album-tab-image']) ?>
			<figcaption class="figure-caption wt-album-tab-caption">
				<a href="<?= Html::escape($media->getRawUrl()) ?>" title="<?= strip_tags($media->getFullName()) ?>">
					<?= $media->getFullName() ?>
				</a>
			</figcaption>
		</figure>
	<?php endforeach ?>
</div>
