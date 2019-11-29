
<!-- /.header -->
<div class="header"> 
    @include('navigators.views.items.items', ['position' => 'header'])
</div>

<div class="body">
    @include('navigators.views.items.items', ['position' => 'content'])
</div>
<!-- /.body -->
<div class="footer">
    @include('navigators.views.items.items', ['position' => 'footer'])
</div>

