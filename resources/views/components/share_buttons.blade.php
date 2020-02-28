<div class="container-fluid {!! isSet($class)? $class:'' !!} share-buttons">
    <a href='https://www.facebook.com/dialog/share?&appId=1429726730641216&version=v2.0&display=popup&href=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2F&redirect_uri=https%3A%2F%2Fdevelopers.facebook.com%2Ftools%2Fexplorer'
       target='_blank' class="no-border" data-toggle="tooltip" title="{{ trans('general.share_facebook') }}">
        <i class='fa fa-facebook fa-lg'></i>
    </a>

    <a href='https://twitter.com/intent/tweet?text={!! $title !!}&url={!! $url !!}'
       target='_blank' class="no-border" data-toggle="tooltip" title='{{ trans('general.share_twitter') }}'>
        <i class='fa fa-twitter fa-lg'></i>
    </a>

    <a href='//www.pinterest.com/pin/create/button/?url={!! $url !!}' class='pin-it-button no-border'
       count-layout='none' target='_blank' data-toggle="tooltip" title="{{ trans('general.share_pinterest') }}">
        <i class='fa fa-pinterest fa-lg'></i>
    </a>
    <a href='mailto:?subject={!! $title !!}, {{trans('informacie.info_gallery_SNG')}}&body={!!$url!!}'
       style="font-size:0.9em" target='_blank' class="no-border" data-toggle="tooltip"
       title="{{ trans('general.share_mail') }}">
        <i class='fa fa-envelope fa-lg'></i>
    </a>
    <span data-toggle="tooltip" title="{{ trans('general.copy_url') }}">
    <a href="#shareLink" style='cursor:pointer' data-toggle="modal" class="no-border" data-target="#shareLink">
        <i class='fa fa-link fa-lg'></i>
    </a>
    </span>
</div>

<!-- Modal -->
<div tabindex="-1" class="modal fade" id="shareLink" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                {{ trans('general.share_link') }}
            </div>
            <div class="modal-body">
                <a href="#"
                   class="pull-right js-copy"
                   data-message="{{ trans('general.copied_to_clipboard') }}" 
                   data-url="{{ $url }}"
                   data-toggle="tooltip"
                   data-trigger="manual"
                   data-container="body"
                   title="{{ trans('general.copy') }}"
                ><i class="fa fa-clipboard" aria-hidden="true"></i> {{ trans('general.copy') }}</a>
                <code>{{ $url }}</code>
            </div>
            <div class="modal-footer">
                <div class="text-center"><button type="button" data-dismiss="modal"
                        class="btn btn-default btn-outline uppercase sans">{{ trans('general.close') }}</button></div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->