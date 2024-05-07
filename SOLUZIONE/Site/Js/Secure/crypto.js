const sha256 = val =>
    crypto.subtle
        .digest('SHA-256', new TextEncoder('utf-8').encode(val))
        .then(h => {
            let hexes = [],
                view = new DataView(h);
            for (let i = 0; i < view.byteLength; i += 4)
                hexes.push(('00000000' + view.getUint32(i).toString(16)).slice(-8));
            return hexes.join('');
        });

// Example usage:
// sha256(
//     JSON.stringify({ a: 'a', b: [1, 2, 3, 4], foo: { c: 'bar' } })
// ).then(console.log);
