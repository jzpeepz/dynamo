@extends(config('dynamo.layout'))

@section('title', $dynamo->getName() . ' Manager')

@section(config('dynamo.target_blade_section', 'content'))
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10">
                <div class="card">
                    {{--*****************************************************************
                        *                         START HEADER BLOCK                    *
                        *            This block is the header of the dynamo card        *
                        *    Includes name of manager, index buttons, and create button *
                        ***************************************************************** --}}
                    <div class="card-header">
                        @if ($dynamo->addVisible())
                            <a href="{{ route($dynamo->getRoute('create')) }}" class="btn btn-success btn-sm float-right">Add {{ $dynamo->getName() }}</a>
                        @endif
                        @foreach ($dynamo->getIndexButtons() as $button)
                            <div class="mr-2 float-right">{!! call_user_func($button) !!}</div>
                        @endforeach
                        {{ $dynamo->getName() }} Manager
                    </div>
                    {{--***************************
                        *     END HEADER BLOCK    *
                        *************************** --}}


                    {{--**************************************
                        *       START SEARCH BAR BLOCK       *
                        *  This block begins the card-body   *
                        *     Includes search bar form       *
                        ************************************** --}}
                    <div class="card-body">

                        @include('dynamo::partials.alerts')

                        @if ($dynamo->hasSearchable())

                            <form action="{{ route($dynamo->getRoute('index')) }}" method="get" class="dynamo-search form-inline">

                                <input type="hidden" name="view" class="form-control" value="{{ request()->input('view') }}">

                                @foreach ($dynamo->getFilters() as $filter)

                                    {!! $filter !!}

                                @endforeach

                                    <div class="form-group mb-3">
                                        <label for="" class="search-label">Search</label>
                                        <div class="input-group dynamo-input-group">
                                            <input type="text" name="q" class="form-control" value="{{ request()->input('q') }}" {!! $dynamo->getSearchOptionsString() !!}>
                                            <div class="input-group-append">
                                                @if (request()->has('q'))
                                                    <a href="{{ route($dynamo->getRoute('index')) }}" class="btn btn-secondary">Clear</a>
                                                @endif
                                                <button class="btn btn-primary" type="submit" id="button-addon2"><i class="fa fa-search"></i> Search</button>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        @endif

                        {{--*******************************
                            *     END SEARCH BAR BLOCK    *
                            ******************************* --}}

                            {{--*********************************************************
                                *                 START INDEXTAB BLOCK                  *
                                *             This block renders indexTabs              *
                                *   Includes indexTabs, count of members in that tab,   *
                                *                and tooltps for each tab               *
                                ********************************************************* --}}

                            @if ($dynamo->hasIndexTabs())
                                <div class="card-header">
                                     <ul class="nav nav-tabs card-header-tabs" id="dynamo-index-nav-tabs" role="tablist">
                                        @foreach ($dynamo->getIndexTabs() as $index => $tab)
                                            <li class="nav-item" id="dynamo-index-tab-margin-bottom">
                                                <a class="nav-link {{ ($index == 0 && ! request()->has('view')) || (request()->input('view') == str_slug($tab->getName())) ? 'active' : '' }}"
                                                    href="{{ route($dynamo->getRoute('index'), ['view' => str_slug($tab->getViewName())]) }}" role="tab">{{ $tab->getName() }}
                                                    @if ($tab->shouldShowCount())
                                                        <span class="round-badge">{{ $dynamo->getIndexItemsQueryBuilder($tab->getViewName())->count() }}</span>
                                                    @endif
                                                    @if ($tab->hasOption('tooltip'))
                                                        <i id="dont-show-on-mobile-tooltip" class="fas fa-question-circle dynamo-tooltip" data-toggle="tooltip" data-html="true"
                                                        title="{!! $tab->getOption('tooltip') !!}"></i>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{--*****************************
                                *     END INDEXTAB BLOCK    *
                                ***************************** --}}


                        {{--**********************************************
                            *               START ITEMS EMPTY BLOCK      *
                            *   If no items, say no items found and link *
                            *        to create a new dynamo item         *
                            ********************************************** --}}
                        @if ($items->isEmpty())

                            <div class="dynamo-index-table-empty">No items found. <a href="{{ route($dynamo->getRoute('create')) }}">Add one.</a></div>

                        @else

                        {{--********************************
                            *     END ITEMS EMPTY BLOCK    *
                            ******************************** --}}

                            {{--*******************************************************
                                *               START TABLE BLOCK                           *
                                *   Render the dynamo table of objects                *
                                *   Includes table head which is the getIndexes()     *
                                *   Includes table body which renders rows of each    *
                                *   dynamo item with their values in each column      *
                                *   and with an edit button linking to the formView   *
                                *   of each dynamo object.                            *
                                ******************************************************* --}}

                            <div class="table-responsive dynamo-table-responsive">
                                <table class="table" id="dynamo-index">
                                    <thead>
                                        <tr>
                                            @foreach ($dynamo->getIndexes() as $index)
                                                <th>{{ $index->label }}</th>
                                            @endforeach
                                            <th class="dynamo-width-of-action-column">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dynamo-index-body">
                                        @foreach ($items as $item)
                                            <tr class="dynamo-index-row" data-id="{{ $item->id }}">
                                                @foreach ($dynamo->getIndexes() as $index)
                                                    <td>{!! $index->getValue($item) !!}</td>
                                                @endforeach
                                                <td class="dynamo-width-of-action-row">
                                                    <a href="{{ route($dynamo->getRoute('edit'), $item->id) }}" style="padding: 0px !important;" class="btn btn-link btn-sm">Edit</a>
                                                    @foreach ($dynamo->getActionButtons() as $button)
                                                        {!! call_user_func($button, $item) !!}
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {!! method_exists($items, 'render') ? $items->appends(request()->all())->render() : null !!}

                            {{--**************************
                                *     END TABLE BLOCK    *
                                ************************** --}}
                        @endif
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

                                If you delete this {{ $dynamo->getName() }}, every single object will be detached from it. <br><br>The objects themselves will not be deleted but they will all be removed
                                from this category, and then this category will be deleted. This will change pages on your website.<br><br>

                                If you want to remove only a few objects from this category, click the Edit button instead.<br><br>

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

        var realcatname = $('.card-heading');

        var path = window.location.pathname;

        categoryNameForAjax = path.replace('/pilot/', '');

        //Show modal if the Manage Button is pressed and set the Category name and it's Id that it was pressed on to a variable
        $('#relationships-manager-modal').on('show.bs.modal', function (e) {
            //Get string of category name, and its data-id attribute value
            var categoryName = e.relatedTarget.closest('.dynamo-index-row').firstChild.nextElementSibling.innerText;
            var categoryDataId = e.relatedTarget.closest('.dynamo-index-row').getAttribute('data-id');

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
                $.post('/pilot/' + categoryNameForAjax + '/' + categoryDataId, { _method: 'delete' }, function(result) {
                    var thisCatsDynamoRow = document.getElementsByClassName('dynamo-index-row');
                    for(i = 0; i < thisCatsDynamoRow.length; i++){
                        if(thisCatsDynamoRow[i].getAttribute('data-id') == categoryDataId)
                        {
                            thisCatsDynamoRow[i].parentNode.removeChild(thisCatsDynamoRow[i])
                        }
                    }
                    $('#relationships-manager-modal').modal('hide');
                });
            })

        });

        //If user clicks out of the modal, remove any text from input field
        //and uncheck the "I Am Sure" checkbox
        $('#relationships-manager-modal').on('hidden.bs.modal', function (e) {
            permaDeleteBtn.classList.add('disabled');
            permaDeleteBtn.disabled = true;
            categoryInputField.value = '';
            categoryCheckbox.checked = false;
        })
    })
    </script>
@endsection
