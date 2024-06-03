import fs from 'fs';

describe('File existence', () => {
    it('should check if the file exists', () => {
        const filePath = './assets/src/scripts/';
        expect(fs.existsSync(filePath)).toBe(true);
    });
});
