@extends(config('dynamo.layout'))

@section('title', ($item->exists ? 'Edit' : 'Add') . ' ' . $dynamo->getName())

@section(config('dynamo.target_blade_section', 'content'))
    <div class="container-fluid pt-4 {{ config('pilot.backend_side_bar_layout') ? 'pl-lg-0 pr-lg-0' : ''}}">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 {{ config('pilot.backend_side_bar_layout') ? 'pl-lg-0 pr-lg-0' : ''}}">
                <div class="card {{ config('pilot.backend_side_bar_layout') ? 'sidebar-card' : ''}} mb-4">
                    <div class="card-header">
                        {{ $item->exists ? 'Edit' : 'Add' }}

                        @if($dynamo->hasFormPanelTitleOverride() == null)
                            {{ $dynamo->getName() }}
                        @else
                            {!! $dynamo->getFormPanelTitleOverride() !!}
                        @endif

                        @if ($item->exists && method_exists($item, 'url'))
                            <a href="{{ $item->url() }}" target="_blank" class="btn btn-info btn-sm float-right"><i class="fa fa-eye"></i> View</a>
                        @endif

                        @foreach ($dynamo->getFormHeaderButtons() as $button)
                            <div class="mr-2 float-right">{!! call_user_func($button) !!}</div>
                        @endforeach
                    </div>

                    <div class="card-body">

                        @include('dynamo::partials.alerts')

                        {!! $dynamo->callViewHook('form_before', $item) !!}

                        {{--***************************************
                            *     If the user uses Formtabs       *
                            *      Run this block to render       *
                            *     the tabs                        *
                            *************************************** --}}

                        @if ($dynamo->hasFormTabs())
                             <ul class="nav nav-tabs" role="tablist">
                                @foreach ($dynamo->getFormTabs() as $index => $tab)

                                    <li role="presentation" class="nav-item">
                                        <a class="nav-link{{ ($index == 0) ? ' active' : '' }}" href="#{{ $tab->key }}" role="tab" aria-controls="{{ $tab->key }}" data-toggle="tab">{{ $tab->getName() }}
                                            @if ($tab->hasOption('tooltip'))
                                                <i class="fas fa-question-circle dynamo-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{!! $tab->getOption('tooltip') !!}"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {!! Form::model($item, $formOptions) !!}

                            <input type="hidden" id="item-id" value="{{ $item->id }}">

                            <div class="tab-content {{ $dynamo->hasFormTabs() ? 'pt-3' : 'pt-0' }}">
                                @if ($dynamo->hasFormTabs())

                                    {{--***************************************
                                        *     If the user uses Formtabs       *
                                        *     Run this block to render        *
                                        *     the form fields in each tab     *
                                        *************************************** --}}

                                    @foreach ($dynamo->getFormTabs() as $index => $tab)
                                        <div role="tabpanel" class="tab-pane{{ ($index == 0) ? ' active' : '' }}"
                                             id="{{ $tab->key }}">

                                             @foreach ($tab->fields as $field)
                                                 {!! $field->render($item) !!}
                                             @endforeach
                                        </div>
                                    @endforeach

                                @else

                                    {{--***************************************
                                        *  If the user does not use Formtabs  *
                                        *   Run this default block to render  *
                                        *   the dynamo form as normally would *
                                        *************************************** --}}

                                    @foreach ($dynamo->getFieldGroups() as $group => $fields)
                                        <fieldset id="{{ $group }}" class="{{ ! empty($group) ? 'well' : '' }} dynamo-group">
                                            @if ($dynamo->hasGroupLabel($group))
                                                <legend class="dynamo-group-label">{{ $dynamo->getGroupLabel($group) }}</legend>
                                            @endif

                                            @foreach ($fields as $field)
                                                {!! $field->render($item) !!}
                                            @endforeach
                                        </fieldset>
                                    @endforeach

                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">
                            @if ($dynamo->hasSaveItemTextChange() == null)
                                Save {{ $dynamo->getName() }}
                            @else
                                {{ $dynamo->getSaveItemText() }}
                            @endif
                            </button>
                            <a href="{{ route($dynamo->getRoute('index'), $dynamo->getRouteParameters('index')) }}" class="btn">Cancel</a>

                            {!! Form::close() !!}

                            @if($dynamo->hasFormFooterButton())
                                <div class="d-flex justify-content-end" style="margin-top: -51px;">
                            @endif

                                @foreach ($dynamo->getFormFooterButtons() as $button)
                                    <div class="mb-0 ml-2">{!! call_user_func($button) !!}</div>
                                @endforeach

                                @if ($item->exists)
                                    @if ($dynamo->deleteVisible())
                                        {!! Form::open(['route' => [$dynamo->getRoute('destroy'), $item->id], 'method' => 'delete', 'class' => 'delete-form dynamo-delete-form float-right d-inline mb-0', 'style' => 'display: inline-block;', 'onsubmit' => 'return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\');']) !!}
                                            <button type="submit" class="btn btn-danger btn-delete dynamo-delete-btn " style="float: right;">Delete</button>
                                        {!! Form::close() !!}
                                    @endif
                                @endif

                            </div>


                            {!! $dynamo->callViewHook('form_after', $item) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--*********************************************
        *      START RELATIONSHIPS MODAL BLOCK      *
        *   If the user uses manage relationships,  *
        *      run this block to include modal      *
        *************************************** --}}
        <div class="modal fade" id="relationships-manager-modal" tabindex="-1" role="dialog">

            <div class="modal-dialog modal-lg" role="document">

                <div class="modal-content">

                    <div class="modal-header">

                        <h4 class="modal-title">Delete Category</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>

                    <div class="modal-body dynamo-modal-body">
                            {{--***************************************
                                *          DANGER ZONE CODE           *
                                *************************************** --}}
                            <div class="dynamo-danger-zone-modal-body">
                                <h1>Danger Zone!</h1>

                                If you delete this {{ $dynamo->getName() }}, every single object will be detached from it. <br><br>

                                For example, the page https://www.mywebsite.org/category/14/dog will no longer exist on your website because there is not a category called Dog in the system anymore.
                                Actually the category gets "soft deleted", which means that FLEX360 can bring it back, but if you are unsure of what things will change on your website,
                                we recommended getting in touch with us so we can help you. <br><br>

                                If you want to remove only a few objects from this category, use the tool above to move items in and out of the category.<br><br>

                                If you are sure, type the name of the category below, check the box, and click Permanatly Delete.<br><br>


                                    <input class="form-control" id="categoryInputField" type="text" name="areYouSure" placeholder="Type the name of the {{ $dynamo->getName() }} to confirm">
                                    <br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="areYouSureCheckbox">
                                        <label class="form-check-label" for="exampleCheck1">Are you sure?</label>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-danger disabled" id="perma-delete-btn">Permanatly Delete This {{ $dynamo->getName() }}</button>
                            </div>

                            {{--***************************************
                                *          DANGER ZONE CODE END       *
                                *************************************** --}}

                    </div>
                    <div class="modal-footer dynamo-modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>

                    <?php /* <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div> */ ;?>

                </div><!-- /.modal-content -->

            </div><!-- /.modal-dialog -->

        </div><!-- /.modal -->

        {{--****************************************
            *     END RELATIONSHIPS MODAL BLOCK    *
            **************************************** --}}
@endsection

@section('scripts')
    {{--***************************************
        *  Script deals with Relationships    *
        *   Manager popup                     *
        *************************************** --}}
    <script>

    $(document).ready(function() {
        //Global variables

        //Get Button
        const permaDeleteBtn = document.getElementById("perma-delete-btn");
        //Get input field
        const categoryInputField = document.getElementById("categoryInputField");
        //Get checkbox
        const categoryCheckbox = document.getElementById("areYouSureCheckbox");
        //Get id of category being deleted for ajax request
        const itemId = document.getElementById("item-id").value;
        //Get current url path.
        var path = window.location.pathname;
        //Change current url path
        categoryNameForAjax = path.replace('/edit', '');

        //Show modal if the Manage Button is pressed and set the Category name and it's Id that it was pressed on to a variable
        $('#relationships-manager-modal').on('show.bs.modal', function (e) {
            //Get string of category name, and its data-id attribute value
            var categoryName = $( ".category-name-for-delete-modal" ).val();

            //Get the name in lowercase format with no spaces to use in the ajax call
            var categoryNameLower = categoryName.toLowerCase();
            categoryNameLower = categoryNameLower.replace(" ", "");

            //If the input's value is the same string as the categoryName AND checkbox is true,
            //then remove the disabled class of perma delete btn so user can press it
            $('#areYouSureCheckbox').change(function(){
                if(categoryInputField.value == categoryName && categoryCheckbox.checked == true)
                {
                        permaDeleteBtn.classList.remove('disabled');
                        permaDeleteBtn.removeAttribute("disabled")
                }
                else
                {
                    permaDeleteBtn.classList.add('disabled');
                    permaDeleteBtn.disabled = true;
                }
            });

            //On click on perma delete button
            //Send ajax request to delete the category from the database
            $('#perma-delete-btn').unbind('click').on('click', function (e) {
                $.post(categoryNameForAjax, { _method: 'delete' }, function(result) {
                    // // Simulate an HTTP redirect:
                    categoryNameForAjax = categoryNameForAjax.replace('//pilot', '');
                    categoryNameForAjax = categoryNameForAjax.replace(/[0-9]+/g, '');
                    window.location.replace(categoryNameForAjax);
                });
            })

        });
    })
    </script>
@endsection
