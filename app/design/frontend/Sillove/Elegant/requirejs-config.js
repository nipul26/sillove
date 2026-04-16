var config = {
    deps: [
        'js/typing-placeholder',
        'js/aos-init'
    ],
    map: {
        '*': {
            aos: 'js/aos'
        }
    },
    shim: {
        'js/aos': {
            exports: 'AOS'
        }
    }
};