<div class="side-menu sidebar-inverse">
    <nav class="navbar navbar-default" role="navigation">
        <div class="side-menu-container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('voyager.dashboard') }}">
                    <div class="logo-icon-container">
                        <?php $admin_logo_img = Voyager::setting('admin_icon_image', ''); ?>
                        @if($admin_logo_img == '')
                            <img src="{{ config('voyager.assets_path') }}/images/logo-icon-light.png" alt="Logo Icon">
                        @else
                            <img src="{{ Voyager::image($admin_logo_img) }}" alt="Logo Icon">
                        @endif
                    </div>
                    <div class="title">{{Voyager::setting('admin_title', 'VOYAGER')}}</div>
                </a>
            </div><!-- .navbar-header -->

            <div class="panel widget center bgimage"
                 style="background-image:url({{ Voyager::image( Voyager::setting('admin_bg_image'), config('voyager.assets_path') . '/images/bg.jpg' ) }});">
                <div class="dimmer"></div>
                <div class="panel-content">
                    <img src="{{ $user_avatar }}" class="avatar" alt="{{ auth()->user()->name }} avatar">
                    <h4>{{ ucwords(auth()->user()->name) }}</h4>
                    <p>{{ auth()->user()->email }}</p>

                    <a href="{{ route('voyager.profile') }}" class="btn btn-primary">Profile</a>
                    <div style="clear:both"></div>
                </div>
            </div>

        </div>

        <div class="navbar-expand-toggle">
        {!! menu('admin', 'admin_menu') !!}
        </div>
    </nav>
</div>
