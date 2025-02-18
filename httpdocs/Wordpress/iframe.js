// Function to create the loader
function createLoader() {
    // Get the iframe element by ID
    const iframe = document.getElementById('gibbs_iframe');

    // Create loader container
    const loader = document.createElement('div');
    loader.id = 'gibbs_loader';
    loader.style.top = '0';
    loader.style.padding = '23px';
    loader.style.left = '0';
    loader.style.right = '0';
    loader.style.bottom = '0';
    loader.style.display = 'flex';
    loader.style.justifyContent = 'center';
    loader.style.alignItems = 'center';
    loader.style.zIndex = '9999';

    // Create spinner (circle)
    const spinner = document.createElement('div');
    spinner.style.border = '5px solid #f3f3f3';  // Light grey
    spinner.style.borderTop = '5px solid #008474';  // Blue
    spinner.style.borderRadius = '50%';
    spinner.style.width = '50px';
    spinner.style.height = '50px';
    spinner.style.margin = '0 auto';
    spinner.style.animation = 'spin 2s linear infinite';

    // Append spinner to loader
    loader.appendChild(spinner);

    // Insert the loader after the iframe
    iframe.parentNode.insertBefore(loader, iframe);
}

// Function to hide the loader
function hideLoader() {
    const loader = document.getElementById('gibbs_loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

// Add keyframes for spinning animation
const style = document.createElement('style');
style.innerHTML = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
        
    .single-listing-page-titlebar, .left-side-divv-single{
        display: none;
    }
`;
document.head.appendChild(style);

// Show loader when the page is loaded and iframe is already present
createLoader();

// Get iframe element
const iframe = document.getElementById('gibbs_iframe');

// Add event listener for iframe load to hide loader when content is loaded
iframe.onload = function() {
    hideLoader();  // Hide loader once iframe content is loaded
};