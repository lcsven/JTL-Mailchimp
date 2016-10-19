{*only show custom messages when using an old version*}
{if isset($modern) && $modern === false}
    {if isset($cFehler) && $cFehler|strlen > 0}
        <p class="box_error">{$cFehler}</p>
    {/if}
    {if isset($cHinweis) && $cHinweis|strlen > 0}
        <p class="box_success">{$cHinweis}</p>
    {/if}
{/if}

{literal}
<script>
    $(document).ready(function() {

        // insert a "check-/un-check all"-handler
        $('#checkAll').change(function() {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

        // insert a "post-handler" for the "search-submit"-field
        $('form[name=subscribers_search]').on('submit', function() {
            uri = document.URL;
            vUrl = uri.split('?'); // cut the URI in two pieces

            var szNewParams = '';
            var vParams = vUrl[1].split('&'); // split parameters in key-value-pairs
            for (var i in vParams) {
                // if there is a 'search-field'
                if (vParams[i].startsWith('cSearchField')) {
                    // drop out its value
                    vParams[i] = vParams[i].substr(0, vParams[i].indexOf('=')) + '=';
                    // set the 'search-field' value as new the value for that parameter
                    var szNewValue = $('form[name=subscribers_search] input[name=cSearchField]').val();
                    // re-build the parameters (at first as a array)
                    vParams[i] += szNewValue;
                }
                // merge a new parameter-string
                if ('' === szNewParams) {
                    szNewParams += vParams[i];
                } else {
                    szNewParams += '&' + vParams[i];
                }
            }
            // merge the uri with new "search"-(or param-)part of the url
            vUrl[0] += '?' + szNewParams;
            // set our new uri as the form-action
            $('form[name=subscribers_search]').attr('action', vUrl[0]);
        });

    });
</script>
{/literal}

<div class="panel panel-default">
    <form name="subscribers_search" method="post" action="">
    {$jtl_token}
    <div id="settings">

        {* search *}
        <div class="input-group">
            <span class="input-group-addon">
                <label for="cSearchField">e-Mail Suche:</label>
            </span>
            {*<input type="hidden" name="kPlugin" value="{$oPlugin->kPlugin}">*}
            <input class="form-control" id="cSearchField" name="cSearchField" type="text" value="{if isset($szSearchString) && $szSearchString|strlen > 0}{$szSearchString}{/if}" />
            <span class="input-group-btn">
                <button name="search" type="submit" class="btn btn-primary" value="email_search">
                    <i class="fa fa-search"></i> Suchen
                </button>
            </span>
        </div>
    </form>

    {* pagination *}
    {if isset($szSearchString)}
        {include file='tpl_inc/pagination.tpl' oPagination=$oPagiMailChimp cParam_arr=['kPlugin'=>$oPlugin->kPlugin,'cSearchField'=>$szSearchString]}
    {else}
        {include file='tpl_inc/pagination.tpl' oPagination=$oPagiMailChimp cParam_arr=['kPlugin'=>$oPlugin->kPlugin]}
    {/if}

    <form name="subscribers" method="post" action="">
        {*<table class="table table-condensed table-striped table-hover">*}
        {*<table class="table table-condensed table-hover">*}
        <table class="table table-hover">
             <tbody>
                <tr>
                    <th></th>
                    <th class="tleft">Abonnent</th>
                    <th class="tleft">Kundengruppe</th>
                    <th class="tleft">E-Mail Adresse</th>
                    <th class="tcenter">Eingetragen</th>
                    <th class="tcenter">Synchronisiert</th>
                    <th class="tcenter">Liste</th>
                    <th class="tcenter">Aktionen</th>
                </tr>
                {if isset($oNewsletterReceiver_arr)}
                     {foreach from=$oNewsletterReceiver_arr item="oNewsletterReceiver"}
                <tr class="tab_bg1">
                    <td><input type="checkbox" name="id_{$oNewsletterReceiver->id}" value="{$oNewsletterReceiver->subscriberHash}"></td>
                    <td class="tleft">
                    {if 'female' === $oNewsletterReceiver->cGender}Frau{else}Herr{/if} {$oNewsletterReceiver->cVorname} {$oNewsletterReceiver->cNachname}
                    </td>
                    <td class="tleft">{$oNewsletterReceiver->cKundengruppe}</td>
                    <td class="tleft">{$oNewsletterReceiver->cEmail}</td>
                    <td class="tcenter">{$oNewsletterReceiver->dEingetragen|date_format:"%d.%m.%Y %R"}</td>
                    <td class="tcenter">
                    {if isset($oNewsletterReceiver->dLastSync)}
                        {$oNewsletterReceiver->dLastSync|date_format:"%d.%m.%Y %R"}
                    {else}
                        n.v.
                    {/if}
                    </td>
                    <td class="tcenter">
                    {if isset($cList) && $oNewsletterReceiver->remote}
                        {$cList}
                    {else}
                        n.v.
                    {/if}
                    </td>

                    <td class="tcenter">
                    {if isset($oNewsletterReceiver->remote) && $oNewsletterReceiver->remote === true}
                        <button class="btn btn-danger btn-xs" type="submit" title="von Liste l&ouml;schen" name="remove" value="{$oNewsletterReceiver->subscriberHash}">
                            <i class="fa fa-remove"></i>
                        </button>
                    {else}
                        <button class="btn btn-success btn-xs" type="submit" title="mit Liste synchronisieren" name="add" value="{$oNewsletterReceiver->subscriberHash}">
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
                    <td>
                        <input type="checkbox" id="checkAll"></input>
                    </td>
                    <td colspan="7">
                        <b id="checkAll">Alle ausw&auml;hlen</b>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="panel-footer">
            <div class="btn-group">
                <button class="btn btn-warning" name="sync" value="sync_part" onclick="document.subscribers.submit">
                    <i class="fa fa-share-square-o"></i> Gew&auml;hlte &uuml;bertragen
                </button>
                <button class="btn btn-danger" name="sync" value="sync_all">
                    <i class="fa fa-share-square-o"></i> Alle &uuml;bertragen
                </button>
            </div>
            &nbsp;&nbsp;&nbsp;
            <button class="btn btn-success" name="reload" value="reload"  onclick="document.reload">
                <i class="fa fa-refresh"></i> Neu einlesen
            </button>
        </div>
    </div>
    </form>
</div>
