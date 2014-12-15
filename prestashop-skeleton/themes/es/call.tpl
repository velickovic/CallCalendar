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
	
	{if isset($call.type) && $call.type != ""}
		<div class="centre-block">
			<div class="div-label">
				<h3>Type:</h3>
			</div>
			<div class="div-content">
				{$call.type}
			</div>
			<br class="clearBoth">
		</div>
	{/if}

	{if isset($call.type) && $call.type != ""}
		<div class="centre-block">
			<div class="div-label">
				<h3>Funding agency:</h3>
			</div>
			<div class="div-content">
				{$call.agency}
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

	{if isset($call.budget) && $call.budget != "0"}
		<div class="centre-block">
			<div class="div-label">
				<h3>Budget:</h3>
			</div>
			<div class="div-content">
				{$call.budget} <!-- |replace:'-00':''|replace:'0000':''} -->
			</div>
			<br class="clearBoth">
		</div>
	{/if}

	{if isset($call.repeating)}
		<div class="centre-block">
			<div class="div-label">
				<h3>Repeating:</h3>
			</div>
			<div class="div-content">
				{$call.repeating|replace:'0':'NO'|replace:'1':'YES'}
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
			{if isset($call.partners) && $call.partners|@count > 0}
				<li><a href="#tabs-3">Partners</a></li>
			{/if}

			<!-- //TODO remove this -->
			{if isset($call) }
				<li><a href="#tabs-4">Attachments</a></li>
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

	{if isset($call.partners) && $call.partners|@count > 0}
		<div id="tabs-3">
			<table id="project-table" class="tablesorter"> 
				<thead> 
					<tr> 
						<th class="partner-list-name">Partner</th> 
						<th class="partner-list-type">Type</th> 
					</tr> 
				</thead> 
				<tbody> 
					{foreach from=$call.partners item=partner name=partners}
					<tr>
						<td class="partner-list-name">
							<a href="{$partner.url}" target="_blank">{$partner.name|escape:'htmlall':'UTF-8'}</a>
						</td>
						<td class="partner-list-type">
							{$partner.type|escape:'htmlall':'UTF-8'}
						</td>
					</tr>
					{/foreach}
				</tbody> 
			</table>
		</div>	
	{/if}


	<!-- //TODO remove this -->
	{if isset($call)}
		<ul id="tabs-4" calss="bullet">
			{foreach from=$attachments item=attachment}
				<li><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}
				</li>

			{/foreach}

		</ul>	
	{/if}

	</div>
		 
</div>

<div class = "right-column">
	{if isset($funding_agency) && $funding_agency|@count > 0}
	<div style="margin-bottom: 20px">
		<img class="logo" src="{$base_url}img/headers/funding-agencies.png" />
		{foreach from=$funding_agency item=fa}
		<div class="row-logo">
			<div class="logo">
				{if $fa.url != ""}
				<a href="{$fa.url}" target="_blank">
				{/if}
					<img class="logo" src="../img/funding-agencies/{$fa.id_funding_agency}.jpg" />
				{if $fa.url != ""}
				</a>
				{/if}
			</div>	
		</div>
		<br class="clearBoth">
		{/foreach}
	</div>	
	{/if}

	<div class="row-logo">
			<div class="logo">
				
				<a href="http://www.es.mdh.se/divisions/18-Division_of_Research_Coordination" target="_blank">
				
					<img class="logo" src="../img/headers/Reco-call-calendar-web.jpg" />
					
					<div style="font-size:11px; padding-top:10px">
						The RECO Call Calendar is maintained by the Division of Research Coordination
					</div>	

				</a>
				
			</div>	
	</div>
</div>

