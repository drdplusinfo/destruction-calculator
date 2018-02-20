var itemType = document.getElementById('itemType');
itemType.addEventListener('OnChange', function () {
    var genericRelated = document.getElementById('genericRelated');
    var volumeRelated = document.getElementById('volumeRelated');
    var statueRelated = document.getElementById('statueRelated');
    if (this.value === 'value') {
        volumeRelated.classList.remove('hidden');
        genericRelated.classList.add('hidden');
        statueRelated.classList.add('hidden');
    }
});