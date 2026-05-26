const sortSelect = document.getElementById('sortselect');
if (sortSelect) {
    sortSelect.addEventListener('change', function() {
        // this.value innehåller ju <sort>-<order>
        // title-asc

        // const arr = this.value.split('-');
        // const sort = arr[0]
        // const order = arr[1]
        // // sort = title
        // order = asc
        const [sort, order] = this.value.split('-');
        //Build url
        // window.location.search = current url query string, ex ?category=1
        const urlSearchParams = new URLSearchParams(window.location.search);
        urlSearchParams.set('sort', sort);
        urlSearchParams.set('order', order);

        
        // redirect to url
        window.location.search = urlSearchParams.toString();
     });    
}