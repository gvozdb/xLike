{*@formatter:off*}

{var $pathes}
<path class="xlike__svg-hand" d="M47.985 68.99c-1.021 0-1.746-.787-1.995-.941-.797-.49-.603-.627-2.045-1.057l-4.038.01c-.503 0-.911-.404-.911-.902v-19.218c0-.499.408-.902.911-.902h5.342c.126-.068.518-.402 1.213-1.187 1.227-1.382 2.312-3.262 3.362-4.927.849-1.346 1.65-2.619 2.496-3.667 1.157-1.439 1.883-1.966 2.584-2.477.721-.522.574-.641 2.009-2.475 2.3-2.938 2.613-4.807 2.917-6.615.054-.32.109-.651.175-.981.259-1.307 1.405-2.659 2.876-2.658.53-.001 1.047.174 1.537.518 1.879 1.319 2.866 5.158 2.494 7.708-.383 2.619-1.578 6.005-2.681 7.23-.958 1.066-1.41 2.122-1.212 3.746.094.773 1.283.804 1.334.806l7.439.028c2.15.018 6.212.69 6.213 4.843 0 1.894-.77 3.071-1.666 3.757.852.758 1.516 1.742 1.278 3.609-.23 1.812-1.128 2.894-2.173 3.448.577.771.967 1.886.712 3.397-.245 1.455-1.134 2.469-2.048 2.961.431.658.699 1.666.473 2.869-.533 2.822-2.977 2.985-4.02 3.056l-.323.021h-22.253z"/>
<path class="xlike__svg-shirt" d="M38.998 46.008h3.018v21h-3.018v-21z"/>
<path class="xlike__svg-jacket" d="M37.68 70.001h-11.067c-1.274 0-2.312-1.04-2.312-2.317l-3.304-21.38c0-1.279 1.037-2.318 2.311-2.318h14.372c1.275 0 2.312 1.039 2.312 2.318v21.38c-.001 1.277-1.038 2.317-2.312 2.317z"/>
{/var}

<div class="xlike [ js-xlike-object ]" data-xlike-propkey="{$propkey}" data-xlike-parent="{$parent}">
    <div class="xlike__line">
        <div class="xlike__line-fill [ js-xlike-stripe ]" style="min-width: {$rating}%;"></div>
    </div>
    <div class="xlike__percent">
        <span class="[ js-xlike-rating ]">{$rating | number_format : 2 : '.' : ''}</span>%
    </div>

    <div class="xlike__items">
        <span class="xlike__item xlike__item_like">
            <{$can ? 'a' : 'span'}
                class="xlike__link {$value > 0 ? 'xlike__link_active' : ''} [ {$can ? 'js-xlike-button' : ''} ]"
                {$can ? 'data-xlike-value="1" href="javascript:undefined;"' : ''}
            >
                <svg class="xlike__icon xlike__icon_like xlike__svg xlike__svg_like icon-svg" viewBox="0 0 100 100">{$pathes}</svg>
                <span class="xlike__count xlike__count_like [ js-xlike-number ]">{$likes | number_format : 0 : '' : ' '}</span>
            </{$can ? 'a' : 'span'}>
        </span>
        <span class="xlike__item xlike__item_dislike">
            <{$can ? 'a' : 'span'}
                class="xlike__link {$value < 0 ? 'xlike__link_active' : ''} [ {$can ? 'js-xlike-button' : ''} ]"
                {$can ? 'data-xlike-value="-1" href="javascript:undefined;"' : ''}
            >
                <svg class="xlike__icon xlike__icon_dislike xlike__svg xlike__svg_dislike icon-svg" viewBox="0 0 100 100">{$pathes}</svg>
                <span class="xlike__count xlike__count_dislike [ js-xlike-number ]">{$dislikes | number_format : 0 : '' : ' '}</span>
            </{$can ? 'a' : 'span'}>
        </span>
    </div>
</div>