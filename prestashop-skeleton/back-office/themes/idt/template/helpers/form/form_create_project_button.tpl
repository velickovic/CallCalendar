<table>
	<tr>
		<td style="padding-bottom:5px;">


			<!-- <input type="button" id="btnCreateProject" name="btnCreateProject" 
			onclick="{
				var user_choice = window.confirm('Are you sure you want to approve this application and create a project form it? Please save all your changes before continuing.');
				if(user_choice==true) {
					alert('Not implemented yet');

				} else {
					return false;
				}
			}" value="Create project" /> -->

			<!-- <input type="button" id="btnCreateProject" name="btnCreateProject" onclick="
				var user_choice = window.confirm('Are you sure you want to approve this application and create a project form it? Please save all your changes before continuing.');
				if(user_choice==true) {
					alert('Not implemented yet');
					
				} else {
					return false;
				}

			" value="Create project" /> -->



			<!-- <form method="post" action="{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}&action=createProject">
    			<input type="submit" id="submitFunction" value="Actualize" name="submitFunction" class="button" >
 			</form> -->

			<input type="button" id="btnCreateProject" name="btnCreateProject" onclick="
				{
					var user_choice = window.confirm('Are you sure you want to approve this application and create a project form it? Please save all your changes before continuing.');
					if(user_choice==true) {
						window.location.search += '&action=createProject';	
						alert('Project Created');
					} else {
						return false;
					}
				}
			" value="Create project" />

			<!-- {$createProject} -->

			<!-- <input type="hidden" name="inputLeaders" id="inputLeaders" value="{foreach from=$leaders item=person}{$person.id_customer}-{/foreach}" />
			<input type="hidden" id="id_project" value="{$id_project}" />
			<input type="hidden" name="nameLeaders" id="nameLeaders" value="{foreach from=$leaders item=person}{$person.firstname|escape:'htmlall':'UTF-8'} {$person.lastname|escape:'htmlall':'UTF-8'}¤{/foreach}" />
			<input type="hidden" name="leaderIdsFromDb" id="leaderIdsFromDb" value="{foreach from=$leaders item=person}{$person.id_customer}-{/foreach}" />
			<input type="hidden" name="leaderNamesFromDb" id="leaderNamesFromDb" value="{foreach from=$leaders item=person}{$person.firstname|escape:'htmlall':'UTF-8'} {$person.lastname|escape:'htmlall':'UTF-8'}¤{/foreach}" />
			
			<div id="ajax_choose_customer">
				<p style="clear:both;margin-top:0;">
					<input type="text" value="" id="leader_autocomplete_input" />
					{l s='Begin typing the first letters of the leaders name, then select the person from the drop-down list'}
				</p>
				<p class="preference_description">{l s='(Do not forget to save afterwards)'}</p>
			</div>
			<div id="divLeaders">
				{foreach from=$leaders item=person}
					<span class="leader">{$person.firstname|escape:'htmlall':'UTF-8'} {$person.lastname|escape:'htmlall':'UTF-8'}
						<span class="delLeader" name="{$person.id_customer}" style="cursor: pointer;">
							<img src="../img/admin/delete.gif" class="middle" alt="" />
						</span>
					</span>
					<br />
				{/foreach}
			</div> -->
		</td>
	</tr>
</table>