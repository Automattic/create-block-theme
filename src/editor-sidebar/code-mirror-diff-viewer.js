/**
 * External dependencies
 */
import CodeMirror from '@uiw/react-codemirror';
import { EditorView, ViewPlugin, Decoration } from '@codemirror/view';
import { EditorState, RangeSetBuilder } from '@codemirror/state';
import { basicSetup } from '@codemirror/basic-setup';
import { json } from '@codemirror/lang-json';
import { diffLines } from 'diff';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';

export function CodeMirrorDiffViewer({ oldCode, newCode }) {
    const [diff, setDiff] = useState([]);

    useEffect(() => {
        // override for testing
        const oldCode = {
            "hello": "123",
            "world": "456",
            "foo": "bar"
        };
        const newCode = {
            "hello": "123",
            "world": "456",
            "foo": "baz"
        };

        setDiff(diffLines(JSON.stringify(oldCode, null, 4), JSON.stringify(newCode, null, 4)));
    }, [oldCode, newCode]);

    const diffDecorations = EditorView.decorations.compute([], (state) => getDiffDecorations(diff));
    // const diffDecorations = (diff) => {
    //     const builder = new RangeSetBuilder();
    //     diff.forEach((part, index) => {
    //         if (part.added || part.removed) {
    //             const className = part.added ? 'added' : 'removed';
    //             const decoration = Decoration.mark({
    //                 class: className,
    //             });
    //             builder.add(index, index + part.value.length, decoration);
    //         }
    //     });
    //     return builder.finish();
    // };

    // const diffPlugin = ViewPlugin.fromClass(class {
    //     constructor(view) {
    //         this.decorations = diffDecorations(diff);
    //     }
    //     update(update) {
    //         if (update.docChanged || update.viewportChanged) {
    //             this.decorations = diffDecorations(diff);
    //         }
    //     }
    // }, {
    //     decorations: v => v.decorations
    // });

    // const state = EditorState.create({
    //     doc: newCode,
    //     extensions: [basicSetup, json(), diffPlugin]
    // });

    // return <CodeMirror state={state} />;

    return (
        <div>
            <CodeMirror
                value={ diff.map(part => part.value).join('') }
                extensions={ [ json(), diffDecorations ] }
				readOnly
			/>
            <style>
                {`
                .cbt-code-mirror-line-added { background-color: #D1F8D9; }
                .cbt-code-mirror-line-removed { background-color: #FFCECB; }
                `}
            </style>
        </div>
    );
}

export function getDiffDecorations(diff) {
    let decorations = [];
    let lineNumber = 0;

    diff.forEach(part => {
        const lines = part.value.split('\n');
        lines.forEach((line, index) => {
            if (line === '') return; // Skip empty lines

            const from = lineNumber + index;
            const to = from; // Single line

            if (part.added) {
                decorations.push(Decoration.line({ class: "cbt-code-mirror-line-added" }).range(from, to));
            } else if (part.removed) {
                decorations.push(Decoration.line({ class: "cbt-code-mirror-line-removed" }).range(from, to));
            }
        });

        lineNumber += lines.length - 1;
    });

    return Decoration.set(decorations);
}

export function mergeDeep(target, ...sources) {
	if (!sources.length) return target;
	const source = sources.shift();

	if (isObject(target) && isObject(source)) {
		for (const key in source) {
            if (isObject(source[key])) {
                if (!target[key]) Object.assign(target, { [key]: {} });
                mergeDeep(target[key], source[key]);
            } else {
                Object.assign(target, { [key]: source[key] });
            }
		}
	}

	return mergeDeep(target, ...sources);
}

function isObject(item) {
	return (item && typeof item === 'object' && !Array.isArray(item));
}
