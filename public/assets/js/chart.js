const {
    merge
  } = window._;
  
  // form config.js
  const echartSetOption = (
    chart,
    userOptions,
    getDefaultOptions,
    responsiveOptions
  ) => {
    const {
        breakpoints,
        resize
    } = window.phoenix.utils;
    const handleResize = options => {
        Object.keys(options).forEach(item => {
            if (window.innerWidth > breakpoints[item]) {
                chart.setOption(options[item]);
            }
        });
    };
  
    const themeController = document.body;
    // Merge user options with lodash
    chart.setOption(merge(getDefaultOptions(), userOptions));
  
    const navbarVerticalToggle = document.querySelector(
        '.navbar-vertical-toggle'
    );
    if (navbarVerticalToggle) {
        navbarVerticalToggle.addEventListener('navbar.vertical.toggle', () => {
            chart.resize();
            if (responsiveOptions) {
                handleResize(responsiveOptions);
            }
        });
    }
  
    resize(() => {
        chart.resize();
        if (responsiveOptions) {
            handleResize(responsiveOptions);
        }
    });
    if (responsiveOptions) {
        handleResize(responsiveOptions);
    }
  
    themeController.addEventListener(
        'clickControl',
        ({
            detail: {
                control
            }
        }) => {
            if (control === 'phoenixTheme') {
                chart.setOption(window._.merge(getDefaultOptions(), userOptions));
            }
        }
    );
  };