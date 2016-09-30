{*only show custom messages when using an old version*}
{if isset($modern) && $modern === false}
    {if isset($cFehler) && $cFehler|strlen > 0}
        <p class="box_error">{$cFehler}</p>
    {/if}
    {if isset($cHinweis) && $cHinweis|strlen > 0}
        <p class="box_success">{$cHinweis}</p>
    {/if}
{/if}

{*
<form class="someform" method="post" action="{$adminURL}">
    {$jtl_token}
    <button name="clear-cache" value="1" class="btn btn-danger" type="submit"><i class="fa fa-trash"></i> Plugin-Cache leeren</button>
</form>
*}

<style>
    table {
        width: 100%;
        border: solid 1px #ccc;
    }
    th {
        padding: 8px;
        /* border: solid 1px #ccc; --TO-CHECK--   */
    }
</style>

<br>
<div id="settings">
	<table>
		 <tbody>
		 	<tr>
				  <th class="tleft">Abonnent</th>
				  <th class="tleft">Kundengruppe</th>
				  <th class="tleft">E-Mail Adresse</th>
				  <th class="tcenter">Eingetragen am</th>
				  <th class="tcenter">Liste</th>
				  <th class="tcenter">Synchronisiert am</th>
				  <th class="tcenter">Letzte Synchronisierung</th>
			 </tr>
             {if isset($oNewsletterReceiver_arr)}
                 {foreach from=$oNewsletterReceiver_arr item="oNewsletterReceiver"}
                 <tr class="tab_bg1">
                      <td class="tleft">
                        {$oNewsletterReceiver->cAnrede} {$oNewsletterReceiver->cVorname} {$oNewsletterReceiver->cNachname}
                      </td>
                      <td class="tleft">{$oNewsletterReceiver->cKundengruppe}</td>
                      <td class="tleft">{$oNewsletterReceiver->cEmail}</td>
                      <td class="tcenter">{$oNewsletterReceiver->dEingetragen|date_format:"%d.%m.%Y %R"}</td>
                      {if isset($oNewsletterReceiver->cList) }
                      <td class="tcenter">{$oNewsletterReceiver->cList}</td>
                      {/if}
                      {if isset($oNewsletterReceiver->dSync)}
                      <td class="tcenter">{$oNewsletterReceiver->dSync|date_format:"%d.%m.%Y %R"}</td>
                      {/if}
                      {if isset($oNewsletterReceiver->dLastSync)}
                      <td class="tcenter">{$oNewsletterReceiver->dLastSync|date_format:"%d.%m.%Y %R"}</td>
                      {/if}
                 </tr>
                 {/foreach}
             {/if}
		</tbody>
	</table>
</div>
