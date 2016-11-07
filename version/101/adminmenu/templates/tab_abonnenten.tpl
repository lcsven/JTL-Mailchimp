{*only show custom messages when using an old version*}
{if isset($modern) && $modern === false}
    {if isset($cFehler) && $cFehler|strlen > 0}
        <p class="box_error">{$cFehler}</p>
    {/if}
    {if isset($cHinweis) && $cHinweis|strlen > 0}
        <p class="box_success">{$cHinweis}</p>
    {/if}
{/if}

<script>
    var szAjaxEndpoint = '{$szAjaxEndpoint}';
</script>
{literal}
<script>
    // catch the "cHinweis"- and "cFehler"-banners
    var oSuccsBanner = $('div[id=content_wrapper][class=container-fluid] > div[class="alert alert-success"]');
    var oErrorBanner = $('div[id=content_wrapper][class=container-fluid] > div[class="alert alert-danger"]');

    $(document).ready(function() {

        // hide the cHinweis- and cFehler-panals at start-up
        // --TODO-- occures very late, but for now ...
        if ('#' === oSuccsBanner.text().trim()) {
            oSuccsBanner.css('display', 'none');
        }
        if ('#' === oErrorBanner.text().trim()) {
            oErrorBanner.css('display', 'none');
        }

        // insert a "check-/un-check all"-handler
        // maybe we should/could use the "global"-js AllMessages() in 'admin/templates/bootstrap/js/global.js'
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
                    // set the 'search-field' value as the new value for that parameter
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
            // merge the uri with a new "search"-(or param-)part of the url
            vUrl[0] += '?' + szNewParams;
            // set our new uri as the form-action
            $('form[name=subscribers_search]').attr('action', vUrl[0]);
        });

    });


    function ajaxAction(szAction, szSubscriberHash) {
        // if no Mailchimp-APIkey is there, we did not procede here in general!
        if ('' === $('input[name=szApiKey]').val()) {
            return false;
        }
        var oUserData = {}; // prepare a user-data-object (later used for json-encoding)
        if ('add' === szAction || 'update' === szAction) {
            // collect our user-data from the hidden-input fields (we do that only for the action "add"!)
            var oFieldData = $('td[id='+szSubscriberHash+']').children(); // get our "user-data"-hidden-fields
            for (i = 0; i < oFieldData.length; i++) {
                // inject our fields as props into an object
                oUserData[$(oFieldData[i]).attr('name')] = $(oFieldData[i]).attr('value');
            }
        }

        var szFormatStore = ''; // to hold the class-string of the clicked element, during ajax-load(er)
        $.ajax({
              method :  'get'
            , cache  :  false // to prevent the caching of the browser (but works only with GET, because there ist a time-hash-param)
            , async  :  true // we did not wait for one operation is finished (but if it's necessary, so use 'false' here)
            , url    :  szAjaxEndpoint
            , data   :  {
                  action           : szAction
                , szSubscriberHash : szSubscriberHash
                , szApiKey         : $('input[name=szApiKey]').val()
                , szListId         : $('input[name=szListId]').val()
                , userData         : JSON.stringify(oUserData)
                , token            : $('[name$=jtl_token]').val()
              }
            , beforeSend: function(request) {
                    request.setRequestHeader('Pragma', 'no-cache'); // to prevent the server to send cached stuff
                    // save the format of clicked button (span.i)
                    szFormatStore = $('span[name='+szAction+'][value='+szSubscriberHash+'] > i').attr('class');
                    // activate the ajax-loader (dot-ring)
                    $('span[name='+szAction+'][value='+szSubscriberHash+'] > i').attr('class', 'fa fa-cog fa-spin');
                    //$('span[name='+szAction+'][value='+szSubscriberHash+'] > i').attr('class', 'fa fa-spinner fa-pulse'); // (enlarges the button!)
                    //$('span[name='+szAction+'][value='+szSubscriberHash+']').attr('class', 'fa fa-cog fa-spin'); // rotation FUN! :D
                }
        })
        .done(function(response) {
            // restore the format of the clicked (span.i)
            $('span[name='+szAction+'][value='+szSubscriberHash+'] > i').attr('class', szFormatStore);

            oResponse = jQuery.parseJSON(response);
            // at first, switch off all banners
            oErrorBanner.css('display', 'none');
            oSuccsBanner.css('display', 'none');

            if('' === oResponse.szErrorMsg) {
                if ('add' === szAction) {
                    // set the "cHinweis"-message
                    oSuccsBanner.html(oResponse.iSuccessCount+' Eintrag hinzugef&uuml;gt.');
                    oSuccsBanner.css('display', 'inherit');
                    // siwtch the action-buttons
                    $('span[name=remove][value='+szSubscriberHash+']').css('display', 'inline-block');
                    $('span[name=update][value='+szSubscriberHash+']').css('display', 'inline-block');
                    $('span[name=add][value='+szSubscriberHash+']').css('display', 'none');

                    // in case of errors, we set a default fill
                    $('td[name=dLastSync][id='+szSubscriberHash+']').text('-');
                    $('td[name=szListName][id='+szSubscriberHash+']').text('-');
                }
                if ('update' === szAction) {
                    // set the "cHinweis"-message
                    oSuccsBanner.html(oResponse.iSuccessCount+' Eintrag aktualisiert.');
                    oSuccsBanner.css('display', 'inherit');
                }
                if ('remove' === szAction) {
                    // set the "cHinweis"-message
                    oSuccsBanner.html(oResponse.iSuccessCount+' Eintrag gel&ouml;scht.');
                    oSuccsBanner.css('display', 'inherit');
                    // siwtch the action-buttons
                    $('span[name=remove][value='+szSubscriberHash+']').css('display', 'none');
                    $('span[name=update][value='+szSubscriberHash+']').css('display', 'none');
                    $('span[name=add][value='+szSubscriberHash+']').css('display', 'inline-block');

                    // reset the sync-time and list-name cols
                    $('td[name=dLastSync][id='+szSubscriberHash+']').text('n.v.');
                    $('td[name=szListName][id='+szSubscriberHash+']').text('n.v.');
                }
                // set the cols with responded values (sync-time and list-name)
                if (null !== oResponse.oRestResponse) {
                    $('td[name=dLastSync][id='+szSubscriberHash+']').text(getNowDateTime());
                    $('td[name=szListName][id='+szSubscriberHash+']').text(oResponse.szListName);
                }
            } else {
                // set the "cFehler"-message
                oErrorBanner.text(oResponse.szErrorMsg);
                oErrorBanner.css('display', 'inherit');
            }
        });
    }

    function getNowDateTime() {
        var oDate         = new Date(oResponse.oRestResponse.last_changed);
        var szLocaleDate  = paddZeroToString(oDate.getDate())+'.'+paddZeroToString(oDate.getMonth()+1)+'.'+oDate.getFullYear();
        var szLocaleTime  = paddZeroToString(oDate.getHours())+':'+paddZeroToString(oDate.getMinutes());
        return (szLocaleDate + ' ' + szLocaleTime);
    }

    function paddZeroToString(iNumber) {
        return (10 > iNumber) ? '0'+iNumber.toString() : iNumber.toString();
    }

</script>
{/literal}

<div class="panel panel-default">
    <form name="subscribers_search" method="post" action="">
    {$jtl_token}
    <input type="hidden" name="szApiKey" value="{$szApiKey}">
    <input type="hidden" name="szListId" value="{$szListId}">
    <div id="settings">

        {* search *}
        <div class="input-group">
            <span class="input-group-addon">
                <label for="cSearchField">e-Mail Suche:</label>
            </span>
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
        {$jtl_token}
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

                    <td style="display:none;" id="{$oNewsletterReceiver->subscriberHash}">
                        <input type="hidden" name="szEmail" value="{$oNewsletterReceiver->cEmail}">
                        <input type="hidden" name="szFirstName" value="{$oNewsletterReceiver->cVorname}">
                        <input type="hidden" name="szLastName" value="{$oNewsletterReceiver->cNachname}">
                        <input type="hidden" name="szGender" value="{$oNewsletterReceiver->cGender}">
                    </td>

                    <td><input type="checkbox" name="id_{$oNewsletterReceiver->id}" value="{$oNewsletterReceiver->subscriberHash}"></td>
                    <td class="tleft">
                    {if 'female' === $oNewsletterReceiver->cGender}Frau{else}Herr{/if} {$oNewsletterReceiver->cVorname} {$oNewsletterReceiver->cNachname}
                    </td>
                    <td class="tleft">{$oNewsletterReceiver->cKundengruppe}</td>
                    <td class="tleft">{$oNewsletterReceiver->cEmail}</td>
                    <td class="tcenter">{$oNewsletterReceiver->dEingetragen|date_format:"%d.%m.%Y %R"}</td>
                    <td class="tcenter" name="dLastSync" id="{$oNewsletterReceiver->subscriberHash}">
                    {if isset($oNewsletterReceiver->dLastSync)}
                        {$oNewsletterReceiver->dLastSync|date_format:"%d.%m.%Y %R"}
                    {else}
                        n.v.
                    {/if}
                    </td>
                    <td class="tcenter" name="szListName" id="{$oNewsletterReceiver->subscriberHash}">
                    {if isset($szListName) && $oNewsletterReceiver->remote}
                        {$szListName}
                    {else}
                        n.v.
                    {/if}
                    </td>

                    <td class="tcenter">
                    {assign var='display' value=(isset($oNewsletterReceiver->remote) && $oNewsletterReceiver->remote === true)}
                        <span style="display:{(true === $display) ? 'inline-block' : 'none'};" class="btn btn-warning btn-xs" name="update" title="aktualisieren" value="{$oNewsletterReceiver->subscriberHash}" onclick="ajaxAction('update', '{$oNewsletterReceiver->subscriberHash}')">
                            <i class="fa fa-refresh"></i>
                        </span>
                        <span style="display:{(true === $display) ? 'inline-block' : 'none'};" class="btn btn-danger btn-xs" name="remove" title="von Liste l&ouml;schen" value="{$oNewsletterReceiver->subscriberHash}" onclick="ajaxAction('remove', '{$oNewsletterReceiver->subscriberHash}')">
                            <i class="fa fa-remove"></i>
                        </span>

                        <span style="display:{(false === $display) ? 'inline-block' : 'none'};" class="btn btn-success btn-xs" name="add" title="mit Liste synchronisieren" value="{$oNewsletterReceiver->subscriberHash}" onclick="ajaxAction('add', '{$oNewsletterReceiver->subscriberHash}')">
                            <i class="fa fa-share-square-o"></i>
                        </span>
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
                <button class="btn btn-warning" name="sync" value="sync_part">
                    <i class="fa fa-share-square-o"></i> Gew&auml;hlte &uuml;bertragen
                </button>
                <button class="btn btn-danger" name="sync" value="sync_all">
                    <i class="fa fa-share-square-o"></i> Alle &uuml;bertragen
                </button>
            </div>
            &nbsp;&nbsp;&nbsp;
            <span class="btn btn-success" name="reload" value="reload"  onclick="document.location.reload()">
                <i class="fa fa-refresh"></i> Neu einlesen
            </span>
        </div>
    </div>
    </form>
</div>
