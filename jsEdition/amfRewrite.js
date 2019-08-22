var amfRewrite = {};

/**
 * decoder
 * @param arrayBuffer
 * @returns Object
 */
amfRewrite.decoder = function (arrayBuffer) {
    const o = new amf.Deserializer(new Uint8Array(arrayBuffer));
    return o.reader.readObject();
};

/**
 * encoder
 * @param string
 * @returns ArrayBuffer
 */
amfRewrite.encoder =  function (string) {
    const obj = JSON.parse(string);
    const wr = new amf.Writer();
    wr.writeObject(obj);
    return new Uint8Array(wr.data).buffer;
};
