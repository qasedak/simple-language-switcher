const { registerBlockType } = wp.blocks;
const { SelectControl } = wp.components;
const { useState, useEffect } = wp.element;
const { __ } = wp.i18n;
const { createElement } = wp.element;
const { apiFetch } = wp;

registerBlockType('simple-language-switcher/translatable-string', {
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const [strings, setStrings] = useState(null);
        
        useEffect(() => {
            apiFetch({ 
                path: '/simple-language-switcher/v1/strings'
            }).then(fetchedStrings => {
                setStrings(fetchedStrings);
            });
        }, []);

        if (!strings) {
            return createElement('p', {}, __('Loading...', 'simple-language-switcher'));
        }

        if (strings.length === 0) {
            return createElement('p', {}, __('No translatable strings found. Please add some in the Settings > Translatable Strings page.', 'simple-language-switcher'));
        }

        const options = strings.map(string => ({
            label: string.value,
            value: string.identifier
        }));

        options.unshift({ 
            label: __('Select a string', 'simple-language-switcher'), 
            value: '' 
        });

        return createElement('div', 
            { className: 'sls-translatable-string-block' },
            [
                createElement(SelectControl, {
                    key: 'select',
                    label: __('Select Translatable String', 'simple-language-switcher'),
                    value: attributes.identifier,
                    options: options,
                    onChange: (identifier) => setAttributes({ identifier })
                }),
                attributes.identifier && createElement('div',
                    { 
                        key: 'preview',
                        className: 'sls-preview'
                    },
                    strings.find(s => s.identifier === attributes.identifier)?.value
                )
            ]
        );
    },

    save: function() {
        return null;
    }
});
