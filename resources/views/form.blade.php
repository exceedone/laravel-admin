<div class="card p-2 custom-border-info">
    <div class="box-header with-border d-flex justify-content-between border-bottom border-light p-1 mb-3">
        <h3 class="box-title">{{ $form->title() }}</h3>

        <div class="box-tools d-flex flex-row-reverse">
            {!! $form->renderTools() !!}
        </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    {!! $form->open(['class' => "form-horizontal"]) !!}

        <div class="box-body">

            @if(!$tabObj->isEmpty())
                @include('admin::form.tab', compact('tabObj'))
            @else
                <div class="fields-group">

                    @if($form->hasRows())
                        @foreach($form->getRows() as $row)
                            {!! $row->render() !!}
                        @endforeach
                    @else
                        @foreach($form->fields() as $field)
                            {!! $field->render() !!}
                        @endforeach
                    @endif


                </div>
            @endif

        </div>
        <!-- /.box-body -->

        {!! $form->renderFooter() !!}

        @foreach($form->getHiddenFields() as $field)
            {!! $field->render() !!}
        @endforeach

        <!-- /.box-footer -->
    {!! $form->close() !!}
</div>

