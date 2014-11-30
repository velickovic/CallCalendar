{capture name=path}{l s='Research call'}{/capture}
<script type="text/javascript">
	$(document).ready(function()
	{
		$( "#tabs" ).tabs({
			collapsible: true
		});
		$(".tablesorter").tablesorter(); 
	});
</script>
<div class="centre-column">
	<h1>{$call.title}</h1>
	<!--{if isset($call.focus) && $call.focus != ""}
		<div class="centre-block">
			<div class="div-label">
				<h3>Focus:</h3>
			</div>
			<div class="div-content">
				{$call.focus}
			</div>
			<br class="clearBoth">
		</div>
	{/if}	-->
	
	{if isset($call.status) && $call.status != ""}
		<div class="centre-block">
			<div class="div-label">
				<h3>Status:</h3>
			</div>
			<div class="div-content">
				{$call.status}
			</div>
			<br class="clearBoth">
		</div>
	{/if}

	{if isset($call.planed_project_start) && $call.planed_project_start != "0000-00-00"}
		<div class="centre-block">
			<div class="div-label">
				<h3>Start date:</h3>
			</div>
			<div class="div-content">
				{$call.planed_project_start|replace:'-00':''|replace:'0000':''}
			</div>
			<br class="clearBoth">
		</div>
	{/if}
	
	{if isset($call.url_to_call) && $call.url_to_call!=''}
	<div class="centre-block">
			<div class="div-label">
				<h3>Url:</h3>
			</div>
			<div class="div-content">
				<a href="{$call.url_to_call}" target="_blank">{$call.url_to_call}</a>
			</div>
			<br class="clearBoth">
		</div>
				{/if}
	<div id="tabs">
		<ul>
			{if isset($call.description) && $call.description != ""}
				<li><a href="#tabs-1">Description</a></li>
			{/if}	
			{if isset($call.requirements) && $call.requirements|@count > 0}
				<li><a href="#tabs-2">Requirements</a></li>
			{/if}
		</ul>
	
	{if isset($call.description) && $call.description != ""}
		<div id="tabs-1">
			{$call.description}
		</div>	
	{/if}
	{if isset($call.requirements) && $call.requirements != ""}
		<div id="tabs-2">
			{$call.requirements}
		</div>	
	{/if}

	</div>
		 
</div>

<div class = "right-column">
	{if isset($funding_agencies) && $funding_agencies|@count > 0}
	<div style="margin-bottom: 20px">
		<img class="logo" src="{$base_url}img/headers/funding-agencies.png" />
		{foreach from=$funding_agencies item=funding_agency}
		<div class="row-logo">
			<div class="logo">
				{if $funding_agency.url != ""}
				<a href="{$funding_agency.url}" target="_blank">
				{/if}
					<img class="logo" src="../img/funding-agencies/{$funding_agency.id_funding_agency}.jpg" />
				{if $funding_agency.url != ""}
				</a>
				{/if}
			</div>	
		</div>
		{/foreach}
	</div>	
	{/if}
</div>

