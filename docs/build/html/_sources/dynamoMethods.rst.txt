Dynamo Methods
==============
This section will list all available methods that you are able to chain onto your Dynamo object that you create inside your Dynamo Controller. For a very simple admin,
you might be able to get away with only using that auto() method which is auto-generated for you, and literally have no work to do. But in the case of a database relationship,
or the case of renaming a field in the form, or sizing a picture a specific way, etc, you need to use the methods below.

.. raw:: html

    <style>
     #collection-method-list > p {
         column-count: 3; -moz-column-count: 3; -webkit-column-count: 3;
         column-gap: 2em; -moz-column-gap: 2em; -webkit-column-gap: 2em;
     }

     #collection-method-list a {
         display: block;
     }
    </style>
    <div id="collection-method-list">
        <p>
           <a href="#method-addField">addField</a>
           <a href="#method-addFilter">addFilter</a>
           <a href="#method-addHandler">addHandler</a>
           <a href="#method-addIndex">addIndex</a>
           <a href="#method-addVisible">addVisible</a>
           <a href="#method-auto">auto</a>
           <a href="#method-clearIndexes">clearIndexes</a>
           <a href="#method-deleteVisible">deleteVisible</a>
           <a href="#method-getBaseClass">getBaseClass</a>
           <a href="#method-getGroupLabel">getGroupLabel</a>
           <a href="#method-getIndex">getIndex</a>
           <a href="#method-getIndexItems">getIndexItems</a>
           <a href="#method-getIndexes">getIndexes</a>
           <a href="#method-getIndexOrderBy">getIndexOrderBy</a>
           <a href="#method-getIndexValue">getIndexValue</a>
           <a href="#method-getField">getField</a>
           <a href="#method-getFieldGroups">getFieldGroups</a>
           <a href="#method-getFieldFromType">getFieldFromType</a>
           <a href="#method-getFields">getFields</a>
           <a href="#method-getFilters">getFilters</a>
           <a href="#method-getName">getName</a>
           <a href="#method-getRoute">getRoute</a>
           <a href="#method-getSearchableKeys">getSearchableKeys</a>
           <a href="#method-getValue">getValue</a>
           <a href="#method-group">group</a>
           <a href="#method-handleSpecialFields">handleSpecialFields</a>
           <a href="#method-hasGroupLabel">hasGroupLabel</a>
           <a href="#method-hasManySimple">hasManySimple</a>
           <a href="#method-hasSearchable">hasSearchable</a>
           <a href="#method-hideAdd">hideAdd</a>
           <a href="#method-hideDelete">hideDelete</a>
           <a href="#method-indexOrderBy">indexOrderBy</a>
           <a href="#method-make">make</a>
           <a href="#method-makeLabel">makeLabel</a>
           <a href="#method-paginate">paginate</a>
           <a href="#method-removeBoth">removeBoth</a>
           <a href="#method-removeField">removeField</a>
           <a href="#method-removeIndex">removeIndex</a>
           <a href="#method-searchable">searchable</a>
           <a href="#method-setGroup">setGroup</a>
           <a href="#method-setGroupLabel">setGroupLabel</a>
           <a href="#method-store">store</a>
           <a href="#method-unsetGroup">unsetGroup</a>
        </p>
    </div>

    <hr>

    <p><a name="method-auto"></a></p>
    <h4 id="collection-method" class="first-collection-method"><code>auto()</code></h4>
    <p>The <code>all</code> method returns the underlying array represented by the collection:</p>
    <pre><code>collect([1, 2, 3])-&gt;all();
    // [1, 2, 3]</code></pre>

    <hr>
