function test() {
    console.log('Before:', typeof isFT, isFT);
    if (isFT) {
        console.log('inside');
    } else {
        console.log('FAILED because isFT is falsy');
    }
    var isFT = true;
    console.log('After:', isFT);
}
test();
