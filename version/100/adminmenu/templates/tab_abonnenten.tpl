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

{literal}
<script>
    $(document).ready(function() {

        $('#checkAll').change(function() {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

    });
</script>
{/literal}

<br>
<form class="subscribers" method="post" action="">
{$jtl_token}
<div id="settings">
	<table class="table table-condensed table-striped table-hover">
		 <tbody>
            <tr>
                <th></th>
                <th class="tleft">Abonnent</th>
                <th class="tleft">Kundengruppe</th>
                <th class="tleft">E-Mail Adresse</th>
                <th class="tcenter">Eingetragen</th>
                <th class="tcenter">Synchronisiert</th>
                <th class="tcenter">Liste</th>
                <th class="tcenter">Aktion</th>
            </tr>
            {if isset($oNewsletterReceiver_arr)}
                 {foreach from=$oNewsletterReceiver_arr item="oNewsletterReceiver"}
            <tr class="tab_bg1">
                <td><input type="checkbox" name="id_{$oNewsletterReceiver->id}" value="{$oNewsletterReceiver->subscriberHash}"></td>
                <td class="tleft">
                {$oNewsletterReceiver->cAnrede} {$oNewsletterReceiver->cVorname} {$oNewsletterReceiver->cNachname}
                </td>
                <td class="tleft">{$oNewsletterReceiver->cKundengruppe}</td>
                <td class="tleft">{$oNewsletterReceiver->cEmail}</td>
                <td class="tcenter">{$oNewsletterReceiver->dEingetragen|date_format:"%d.%m.%Y %R"}</td>
                <td class="tcenter">
                {if isset($oNewsletterReceiver->dLastSync)}
                    {$oNewsletterReceiver->dLastSync|date_format:"%d.%m.%Y %R"}
                {/if}
                </td>
                <td class="tcenter">
                {if isset($cList) && $oNewsletterReceiver->remote}
                    {$cList}
                {/if}
                </td>

                <td class="tcenter">
                {if isset($oNewsletterReceiver->remote) && $oNewsletterReceiver->remote === true}
                    <button class="btn btn-danger btn-xs" type="submit" title="von Liste l&ouml;schen" name="remove" value="{$oNewsletterReceiver->subscriberHash}">
                        <i class="fa fa-remove"></i>
                    </button>
                {else}
                    <button class="btn btn-success btn-xs" title="mit Liste synchronisieren" name="add" value="{$oNewsletterReceiver->subscriberHash}">
                        <i class="fa fa-share-square-o"></i>
                    </button>
                {/if}
                </td>

            </tr>
                 {/foreach}
            {/if}
		</tbody>
        <tfoot>
            <tr>
                <td colspan="8">
                    <input type="checkbox" id="checkAll"></input>
                </td>
            </tr>
        </tfoot>
	</table>
    <button class="btn btn-warning" name="sync" value="sync_part" onclick="document.subscribers.submit">
        <i class="fa fa-share-square-o"></i> Gew&auml;hlte &uuml;bertragen
    </button>
    <button class="btn btn-danger" name="sync" value="sync_all">
        <i class="fa fa-share-square-o"></i> Alle &uuml;bertragen
    </button>
    &nbsp;&nbsp;&nbsp;
    <button class="btn btn-success" name="reload" value="reload"  onclick="document.reload">
        <i class="fa fa-refresh"></i> Neu einlesen
    </button>
</div>
</form>
