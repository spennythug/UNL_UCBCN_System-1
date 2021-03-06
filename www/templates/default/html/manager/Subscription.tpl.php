<?php
	$crumbs = new stdClass;
	$crumbs->crumbs = array(
		"Events Manager" => "/manager",
		$context->calendar->name => $context->calendar->getManageURL(),
		"Subscriptions" => NULL
	);
	echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<?php if (count($context->getSubscriptions()) > 0): ?>
<h1 class="wdn-brand">
	Current Subscriptions
</h1>
<div>
	<table>
		<thead>
			<tr>
				<th>Title</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
	    <?php foreach($context->getSubscriptions() as $subscription): ?>
			<tr>
				<td>
		        	<?php echo $subscription->name; ?>
				</td>
				<td class="small-center">
			        <a class="wdn-button wdn-button-brand" href="<?php echo $subscription->getEditURL() ?>">Edit</a>
			        <span class="small-hidden">|</span><br class="hidden small-block"><br class="hidden small-block">
			        <form method="POST" action="<?php echo $subscription->getDeleteURL() ?>" class="delete-form">
		                <input type="hidden" name="subscription_id" value="<?php echo $subscription->id ?>" />
		                <button type="submit">Delete</button>
		            </form>
				</td>
			</tr>
	    <?php endforeach; ?>
		</tbody>
	</table>
</div>
<br>
<?php endif; ?>

<a href="<?php echo $base_manager_url . $context->calendar->shortname ?>/subscriptions/new/" 
	class="wdn-button wdn-button-brand">+ Add a Subscription
</a>
<br>