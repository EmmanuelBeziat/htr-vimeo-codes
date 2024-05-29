<div class="page-wrap">
	<div class="htrvc-admin-view">
		<h1>Importer des codes Viméo</h1>

		<?php
		// Display validation error messages
		if (isset($entry_messages['error'])) {
			foreach ($entry_messages['error'] as $error) {
				echo '<div class="error"><p>' . esc_html($error) . '</p></div>';
			}
		}

		// Display success messages
		if (isset($entry_messages['success'])) {
			foreach ($entry_messages['success'] as $success) {
				echo '<div class="updated"><p>' . esc_html($success) . '</p></div>';
			}
		}

		$entriesCount = count($entries);
		?>

		<p>Copier-coller ici une liste de codes pour les ajouter à la base de données. <br>Les codes peuvent être séparés par les caractères suivants : <kbd>,</kbd>, <kbd>;</kbd>, <kbd> </kbd> (espace ou retour à la ligne).</p>

		<form method="post" id="vimeoCodesForm">
			<div class="form-group">
				<textarea name="code" id="vimeoCodesInput" class="form-textarea" aria-label="Liste de codes" placeholder="Code1, Code2, Code3"></textarea>
			</div>
			<div class="form-group">
				<button type="submit" name="add_entry" id="vimeoCodesSubmit" class="button-primary">Ajouter les codes à la liste</button>
			</div>
		</form>

		<hr class="htrvc-separator">

		<h2><?= $entriesCount === 0 ? 'Aucun code' : ($entriesCount === 1 ? '1 code' : $entriesCount . ' codes') ?> actuellement dans la base de données</h2>

		<?php if ($entriesCount) : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th>Code</th>
					<th>Date de création</th>
					<th width="130">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($entries as $entry) : ?>
				<tr>
					<td><?= esc_html($entry->code); ?></td>
					<td><?= esc_html($entry->created_at); ?></td>
					<td>
						<form method="post">
							<input type="hidden" name="id_entry" value="<?= $entry->id; ?>">
							<button type="submit" class="button-link" name="delete_entry">Supprimer</button>
						</form>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
</div>
