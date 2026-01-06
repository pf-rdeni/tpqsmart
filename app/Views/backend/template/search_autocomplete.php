<!-- Autocomplete Search Styles -->
<style>
    .search-autocomplete {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 0.25rem 0.25rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1050;
        margin-top: 0;
    }

    .search-autocomplete-loading,
    .search-autocomplete-empty {
        padding: 1rem;
        text-align: center;
        color: #6c757d;
        font-size: 0.875rem;
    }

    .search-autocomplete-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .search-autocomplete-item:last-child {
        border-bottom: none;
    }

    .search-autocomplete-item:hover,
    .search-autocomplete-item.active {
        background-color: #f8f9fa;
    }

    .search-autocomplete-item-icon {
        width: 30px;
        text-align: center;
        color: #007bff;
        font-size: 1.1rem;
    }

    .search-autocomplete-item-content {
        flex: 1;
    }

    .search-autocomplete-item-title {
        font-weight: 500;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .search-autocomplete-item-category {
        font-size: 0.75rem;
        color: #6c757d;
    }

    /* Dark mode */
    .dark-mode .search-autocomplete {
        background: #343a40;
        border-color: #495057;
    }

    .dark-mode .search-autocomplete-item {
        border-bottom-color: #495057;
    }

    .dark-mode .search-autocomplete-item:hover,
    .dark-mode .search-autocomplete-item.active {
        background-color: #495057;
    }

    .dark-mode .search-autocomplete-item-title {
        color: #fff;
    }

    .dark-mode .search-autocomplete-item-category {
        color: #adb5bd;
    }

    .dark-mode .search-autocomplete-loading,
    .dark-mode .search-autocomplete-empty {
        color: #adb5bd;
    }
</style>

<!-- Autocomplete Search Script -->
<script>
// Wait for jQuery to be available
(function() {
    function initAutocomplete() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            setTimeout(initAutocomplete, 100);
            return;
        }
        
$(document).ready(function() {
    const searchInput = $('#navbarSearchInput');
    const searchForm = $('#navbarSearchForm');
    const autocompleteContainer = $('#searchAutocomplete');
    const autocompleteResults = $('.search-autocomplete-results');
    const autocompleteLoading = $('.search-autocomplete-loading');
    const autocompleteEmpty = $('.search-autocomplete-empty');
    
    let searchTimeout = null;
    let currentIndex = -1;
    let suggestions = [];

    // Handle input changes
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Reset index
        currentIndex = -1;

        if (query.length >= 2) {
            // Show loading
            autocompleteLoading.show();
            autocompleteResults.empty();
            autocompleteEmpty.hide();
            autocompleteContainer.show();

            // Debounce search
            searchTimeout = setTimeout(function() {
                fetchSuggestions(query);
            }, 300);
        } else {
            // Hide autocomplete if query too short
            autocompleteContainer.hide();
        }
    });

    // Fetch suggestions via AJAX
    function fetchSuggestions(query) {
        $.ajax({
            url: '<?= base_url('backend/search/suggestions') ?>',
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function(data) {
                autocompleteLoading.hide();
                suggestions = data;
                
                if (data.length > 0) {
                    renderSuggestions(data);
                    autocompleteEmpty.hide();
                } else {
                    autocompleteResults.empty();
                    autocompleteEmpty.show();
                }
            },
            error: function() {
                autocompleteLoading.hide();
                autocompleteResults.empty();
                autocompleteEmpty.show();
            }
        });
    }

    // Render suggestions
    function renderSuggestions(data) {
        autocompleteResults.empty();
        
        data.forEach(function(item, index) {
            const itemHtml = `
                <div class="search-autocomplete-item" data-index="${index}" data-url="${item.url}">
                    <div class="search-autocomplete-item-icon">
                        <i class="${item.icon}"></i>
                    </div>
                    <div class="search-autocomplete-item-content">
                        <div class="search-autocomplete-item-title">${item.title}</div>
                        <div class="search-autocomplete-item-category">${item.category}</div>
                    </div>
                </div>
            `;
            autocompleteResults.append(itemHtml);
        });

        // Handle click on suggestion
        $('.search-autocomplete-item').on('click', function() {
            const url = $(this).data('url');
            window.location.href = url;
        });
    }

    // Handle keyboard navigation
    searchInput.on('keydown', function(e) {
        const items = $('.search-autocomplete-item');
        
        if (items.length === 0) return;

        // Arrow Down
        if (e.keyCode === 40) {
            e.preventDefault();
            currentIndex = (currentIndex + 1) % items.length;
            updateActiveItem(items);
        }
        // Arrow Up
        else if (e.keyCode === 38) {
            e.preventDefault();
            currentIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
            updateActiveItem(items);
        }
        // Enter
        else if (e.keyCode === 13) {
            if (currentIndex >= 0 && currentIndex < items.length) {
                e.preventDefault();
                const url = $(items[currentIndex]).data('url');
                window.location.href = url;
            }
        }
        // Escape
        else if (e.keyCode === 27) {
            autocompleteContainer.hide();
            currentIndex = -1;
        }
    });

    // Update active item
    function updateActiveItem(items) {
        items.removeClass('active');
        if (currentIndex >= 0 && currentIndex < items.length) {
            $(items[currentIndex]).addClass('active');
            // Scroll into view if needed
            items[currentIndex].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }

    // Hide autocomplete when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.navbar-search-block').length) {
            autocompleteContainer.hide();
            currentIndex = -1;
        }
    });

    // Prevent form submission if autocomplete is visible and item is selected
    searchForm.on('submit', function(e) {
        if (currentIndex >= 0 && suggestions.length > 0) {
            e.preventDefault();
            const url = suggestions[currentIndex].url;
            window.location.href = url;
        }
    });
});
    } // End initAutocomplete
    
    initAutocomplete();
})();
</script>
