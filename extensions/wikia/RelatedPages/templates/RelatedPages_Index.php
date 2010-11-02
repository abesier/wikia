<?php if( !$skipRendering ) { ?>
	<nav class="RelatedPagesModule">
		<h2><?= wfMsg('wikiarelatedpages-heading') ?></h2>
		<ul>
		<?php foreach($pages as $page) { ?>
			<li>
				<?php if( isset( $page['imgUrl'] ) ) { ?>
				<a href="<?= $page['url']; ?>"><img data-src="<?= $page['imgUrl']; ?>" width="200" height="100"></a>
				<?php } else { ?>
				<div class="articleSnippet"><p><?= $page['text']; ?></p></div>
				<?php } ?>
				<a href="<?= $page['url']; ?>" class="more"><?= $page['title'] ?></a>
			</li>
		<?php } ?>
		</ul>
	</nav>
<?php } // !skipRendering ?>