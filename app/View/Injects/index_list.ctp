<?php
echo $this->Html->script('/vendor/handlebars', array('inline' => false));
echo $this->Html->script('injectengine', array('inline' => false));
?>

<div class="row">
	<div class="col-md-12">
		<h2>Inject Inbox</h2>

		<ul class="nav nav-tabs">
			<li class="active"><a href="#active_injects" data-toggle="tab">ACTIVE</a></li>
			<li class=""><a href="#all_injects" data-toggle="tab">ALL</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane fade in active" id="active_injects">
				<div class="list-group">
					<button type="button" class="list-group-item">Loading...</button>
				</a>
				</div>
			</div>

			<div class="tab-pane fade" id="all_injects">
				<div class="list-group">
					<button type="button" class="list-group-item">Loading...</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script id="inject-list-tpl" type="text/x-handlebars-template">
<a href="{{ injectURL }}/view/{{ id }}" class="list-group-item">
	{{#if submitted}}
	<span class="btn btn-success pull-right">COMPLETED</span>
	{{else}}
		{{#if expired}}
		<span class="btn btn-danger pull-right">EXPIRED</span>
		{{else}}
		<span class="btn btn-info pull-right">ACTIVE</span>
		{{/if}}
	{{/if}}
	
	<h4 class="list-group-item-heading">{{ title }}</h4>
	<p class="text-muted">
		Start: {{ start }}<br />
		End: {{ end }}
	</p>
</a>
</script>

<script id="inject-list-empty-tpl" type="text/x-handlebars-template">
<a href="#" class="list-group-item">
	<h4 class="list-group-item-heading">There is no currently no injects active</h4>
</a>
</script>

<script>
$(document).ready(function() {
	InjectEngine.init();
});
</script>