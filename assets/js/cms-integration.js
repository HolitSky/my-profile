/**
 * CMS Integration Script
 * Fetch data from API and update frontend dynamically
 */

// Configuration
const API_URL = './api/index.php'; // Relative path
const CACHE_KEY = 'portfolio_data';
const CACHE_DURATION = 0; // Disable cache (set to 0 for always fresh data)
// const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes (uncomment for caching)

// Check if we should use API or static content
const USE_CMS = true; // Set to false to use static HTML content

/**
 * Fetch data from API with caching
 */
async function fetchPortfolioData() {
    try {
        // Check cache first
        const cached = localStorage.getItem(CACHE_KEY);
        if (cached) {
            const { data, timestamp } = JSON.parse(cached);
            if (Date.now() - timestamp < CACHE_DURATION) {
                console.log('Using cached data');
                return data;
            }
        }

        // Fetch from API
        const response = await fetch(API_URL);
        const result = await response.json();

        if (result.success) {
            // Cache the data
            localStorage.setItem(CACHE_KEY, JSON.stringify({
                data: result.data,
                timestamp: Date.now()
            }));
            return result.data;
        } else {
            throw new Error(result.error || 'Failed to fetch data');
        }
    } catch (error) {
        console.error('Error fetching portfolio data:', error);
        return null;
    }
}

/**
 * Update About Section
 */
function updateAboutSection(about) {
    if (!about) return;

    console.log('Updating about section...');

    // Update title
    const title = document.querySelector('.about-text .article-title');
    if (title && about.title) {
        title.textContent = about.title;
    }

    // Get all paragraph elements in about-text
    const aboutSection = document.querySelector('.about-text');
    if (!aboutSection) return;

    const paragraphs = aboutSection.querySelectorAll('p');
    
    // Split description by double newlines (paragraph separator)
    const descParagraphs = about.description.split('\n\n').filter(p => p.trim());
    
    // Update existing paragraphs or create new ones
    descParagraphs.forEach((content, i) => {
        if (paragraphs[i]) {
            // Update existing paragraph
            paragraphs[i].textContent = content.trim();
        } else {
            // Create new paragraph if needed
            const p = document.createElement('p');
            p.textContent = content.trim();
            aboutSection.appendChild(p);
        }
    });
    
    // Remove extra paragraphs if any
    for (let i = descParagraphs.length; i < paragraphs.length; i++) {
        paragraphs[i].remove();
    }
}

/**
 * Update Contact Info
 */
function updateContactInfo(contact) {
    if (!contact) return;

    // Email
    const emailLink = document.querySelector('a[href^="mailto:"]');
    if (emailLink && contact.email) {
        emailLink.href = `mailto:${contact.email}`;
        emailLink.textContent = contact.email;
    }

    // Phone
    const phoneLink = document.querySelector('a[href^="tel:"]');
    if (phoneLink && contact.phone) {
        phoneLink.href = `tel:${contact.phone}`;
        phoneLink.textContent = contact.phone;
    }

    // Birthday
    const birthday = document.querySelector('time[datetime]');
    if (birthday && contact.birthday) {
        const date = new Date(contact.birthday);
        birthday.setAttribute('datetime', contact.birthday);
        birthday.textContent = date.toLocaleDateString('en-US', { 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        });
    }

    // Location
    const location = document.querySelector('address');
    if (location && contact.location) {
        location.textContent = contact.location;
    }

    // Social Links
    if (contact.linkedin) {
        const linkedinLink = document.querySelector('a[href*="linkedin"]');
        if (linkedinLink) linkedinLink.href = contact.linkedin;
    }

    if (contact.instagram) {
        const instagramLink = document.querySelector('a[href*="instagram"]');
        if (instagramLink) instagramLink.href = contact.instagram;
    }

    if (contact.github) {
        const githubLink = document.querySelector('a[href*="github"]');
        if (githubLink) githubLink.href = contact.github;
    }
}

/**
 * Update Skills Section (Technologies I Work With)
 */
function updateSkills(skills) {
    if (!skills || skills.length === 0) return;

    // Update Technologies section
    const techContainer = document.querySelector('.service-skills');
    if (techContainer) {
        techContainer.innerHTML = '';
        
        skills.forEach(skill => {
            const techItem = document.createElement('div');
            techItem.className = 'service-skill';
            techItem.innerHTML = `
                <img src="${skill.icon}" alt="${skill.name} icon" width="40" />
                <p>${skill.name}</p>
            `;
            techContainer.appendChild(techItem);
        });
    }
}

/**
 * Update Experience Timeline
 */
function updateExperience(experiences) {
    if (!experiences || experiences.length === 0) return;

    const timeline = document.querySelector('.experience-timeline');
    if (!timeline) return;

    console.log(`Updating experience... (${experiences.length} items)`);

    // Get existing timeline items
    const items = timeline.querySelectorAll('.timeline-item');
    
    experiences.forEach((exp, index) => {
        let item = items[index];
        
        // If item doesn't exist, create new one
        if (!item) {
            item = document.createElement('li');
            item.className = 'timeline-item';
            timeline.appendChild(item);
        }
        
        // Update title
        let title = item.querySelector('.timeline-item-title');
        if (title) {
            title.textContent = exp.title;
        }
        
        // Update period
        let span = item.querySelector('span');
        if (span) {
            span.textContent = exp.period;
        }
        
        // Update exp-text paragraphs
        let expTexts = item.querySelectorAll('.exp-text');
        
        // Build content array from description
        let contents = [];
        if (exp.description && exp.description.trim()) {
            const paragraphs = exp.description.split('\n').filter(p => p.trim());
            contents.push(...paragraphs.map(p => p.trim()));
        }
        
        // Update existing paragraphs or create new ones
        contents.forEach((content, i) => {
            if (expTexts[i]) {
                expTexts[i].textContent = content;
            } else {
                const p = document.createElement('p');
                p.className = 'exp-text';
                p.textContent = content;
                item.appendChild(p);
            }
        });
        
        // Remove extra paragraphs if any
        for (let i = contents.length; i < expTexts.length; i++) {
            expTexts[i].remove();
        }
    });
}

/**
 * Update Education Timeline
 */
function updateEducation(education) {
    if (!education || education.length === 0) return;

    const timeline = document.querySelector('.education-timeline');
    if (!timeline) return;

    console.log(`Updating education... (${education.length} items)`);

    // Get existing timeline items
    const items = timeline.querySelectorAll('.timeline-item');
    
    education.forEach((edu, index) => {
        let item = items[index];
        
        // If item doesn't exist, create new one
        if (!item) {
            item = document.createElement('li');
            item.className = 'timeline-item';
            timeline.appendChild(item);
        }
        
        // Update title
        let title = item.querySelector('.timeline-item-title');
        if (title) {
            title.textContent = edu.institution;
        }
        
        // Update period
        let span = item.querySelector('span');
        if (span) {
            span.textContent = edu.period;
        }
        
        // Update exp-text paragraphs
        let expTexts = item.querySelectorAll('.exp-text');
        
        // Build content array
        let contents = [];
        if (edu.degree && edu.degree.trim()) {
            contents.push(edu.degree.trim());
        }
        if (edu.description && edu.description.trim()) {
            const paragraphs = edu.description.split('\n').filter(p => p.trim());
            contents.push(...paragraphs.map(p => p.trim()));
        }
        
        // Update existing paragraphs or create new ones
        contents.forEach((content, i) => {
            if (expTexts[i]) {
                expTexts[i].textContent = content;
            } else {
                const p = document.createElement('p');
                p.className = 'exp-text';
                p.textContent = content;
                item.appendChild(p);
            }
        });
        
        // Remove extra paragraphs if any
        for (let i = contents.length; i < expTexts.length; i++) {
            expTexts[i].remove();
        }
    });
}

/**
 * Update Portfolio Projects
 */
function updatePortfolio(projects) {
    if (!projects || projects.length === 0) return;

    const projectList = document.querySelector('.project-list');
    if (!projectList) return;

    // Clear existing static HTML
    projectList.innerHTML = '';

    console.log(`Updating portfolio... (${projects.length} projects)`);

    projects.forEach(project => {
        const item = document.createElement('li');
        item.className = 'project-item active';
        item.setAttribute('data-filter-item', '');
        item.setAttribute('data-category', project.category.toLowerCase());
        
        // Use project link and image from database
        const projectLink = project.demo_url || '#';
        const projectImage = project.image || './assets/images/project-placeholder.jpg';
        const projectImageWebp = project.image_webp || null;
        
        console.log(`Portfolio item: ${project.title}, Image: ${projectImage}, WebP: ${projectImageWebp}`);
        
        // Build picture tag with WebP support
        let imageHTML = '';
        if (projectImageWebp) {
            imageHTML = `
                <picture>
                    <source srcset="${projectImageWebp}" type="image/webp">
                    <img src="${projectImage}" alt="${project.title}" loading="lazy" />
                </picture>
            `;
        } else {
            imageHTML = `<img src="${projectImage}" alt="${project.title}" loading="lazy">`;
        }
        
        item.innerHTML = `
            <a href="${projectLink}" target="_blank">
                <figure class="project-img">
                    <div class="project-item-icon-box">
                        <ion-icon name="eye-outline"></ion-icon>
                    </div>
                    ${imageHTML}
                </figure>
                <h3 class="project-title">${project.title}</h3>
                <p class="project-category">${project.category}</p>
            </a>
        `;
        projectList.appendChild(item);
    });
    
    // Re-initialize filter functionality after loading portfolio
    initializePortfolioFilter();
}

/**
 * Initialize Portfolio Filter
 * Call this after portfolio items are loaded dynamically
 */
function initializePortfolioFilter() {
    const filterBtn = document.querySelectorAll("[data-filter-btn]");
    const filterItems = document.querySelectorAll("[data-filter-item]");
    const selectValue = document.querySelector("[data-selecct-value]");
    const select = document.querySelector("[data-select]");
    const selectItems = document.querySelectorAll("[data-select-item]");
    
    if (!filterBtn.length || !filterItems.length) return;
    
    // Filter function
    const filterFunc = function (selectedValue) {
        for (let i = 0; i < filterItems.length; i++) {
            if (selectedValue === "all") {
                filterItems[i].classList.add("active");
            } else if (selectedValue === filterItems[i].dataset.category) {
                filterItems[i].classList.add("active");
            } else {
                filterItems[i].classList.remove("active");
            }
        }
    };
    
    // Add event to filter buttons
    let lastClickedBtn = filterBtn[0];
    
    for (let i = 0; i < filterBtn.length; i++) {
        // Remove old listeners by cloning
        const newBtn = filterBtn[i].cloneNode(true);
        filterBtn[i].parentNode.replaceChild(newBtn, filterBtn[i]);
        
        newBtn.addEventListener("click", function () {
            let selectedValue = this.innerText.toLowerCase();
            if (selectValue) selectValue.innerText = this.innerText;
            filterFunc(selectedValue);
            
            lastClickedBtn.classList.remove("active");
            this.classList.add("active");
            lastClickedBtn = this;
        });
    }
    
    // Add event to select items (mobile dropdown)
    if (selectItems && select) {
        selectItems.forEach(item => {
            const newItem = item.cloneNode(true);
            item.parentNode.replaceChild(newItem, item);
            
            newItem.addEventListener("click", function () {
                let selectedValue = this.innerText.toLowerCase();
                if (selectValue) selectValue.innerText = this.innerText;
                select.classList.remove("active");
                filterFunc(selectedValue);
            });
        });
    }
    
    console.log('Portfolio filter initialized');
}

/**
 * Update Services Section
 */
function updateServices(services) {
    if (!services || services.length === 0) return;

    const serviceList = document.querySelector('.service-list');
    if (!serviceList) return;

    // Clear existing static HTML
    serviceList.innerHTML = '';

    console.log(`Updating services... (${services.length} services)`);

    services.forEach(service => {
        const item = document.createElement('li');
        item.className = 'service-item';
        item.innerHTML = `
            <div class="service-icon-box">
                <img src="./assets/images/${service.icon || 'icon-design.svg'}" 
                     alt="${service.title}" width="40">
            </div>
            <div class="service-content-box">
                <h4 class="h4 service-item-title">${service.title}</h4>
                <p class="service-item-text">${service.description}</p>
            </div>
        `;
        serviceList.appendChild(item);
    });
}

/**
 * Initialize CMS Integration
 */
async function initCMS() {
    if (!USE_CMS) {
        console.log('CMS integration disabled');
        return;
    }

    console.log('Initializing CMS integration...');

    const data = await fetchPortfolioData();
    
    if (!data) {
        console.warn('Failed to load CMS data, using static content');
        return;
    }

    // Update all sections
    updateAboutSection(data.about);
    updateContactInfo(data.contact);
    updateSkills(data.skills);
    updateExperience(data.experience);
    updateEducation(data.education);
    updatePortfolio(data.portfolio);
    updateServices(data.services);

    console.log('CMS integration completed');
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCMS);
} else {
    initCMS();
}

// Refresh data on visibility change (when user returns to tab)
document.addEventListener('visibilitychange', () => {
    if (!document.hidden && USE_CMS) {
        // Clear cache and refresh
        localStorage.removeItem(CACHE_KEY);
        initCMS();
    }
});
