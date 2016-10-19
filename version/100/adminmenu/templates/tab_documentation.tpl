{literal}
<style>
    div.markdown {
        padding: 0px 10px;
    }
    div.markdown ul li {
        list-style: outside none disc;
    }
    div.markdown ol li {
        list-style: outside none decimal;
    }
    div.markdown p {
        text-align: justify;
    }
    div.markdown blockquote {
        font-size: inherit;
    }
</style>
{/literal}
<div class="panel panel-default">
    <div style="padding:5px 40px 30px;">
        {if $fMarkDown}
        <div class="markdown">
            {$szReadmeContent}
        </div>
        {else}
        <pre>
HINWEIS:

Um die Markdown-Darstellung dieser Dokumentation zu erhalten, f&uuml;hren Sie bitte,
im Verzeichnis:

shop/includes/

den Befehl:

$> composer require "erusev/parsedown:dev-master"

aus.</pre>
        <br>
        <pre>{$szReadmeContent}</pre>
        {/if}
    </div>
</div>
