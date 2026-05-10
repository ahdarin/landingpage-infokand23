document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('grainient-container');
    if (!container) return;

    const canvas = document.createElement('canvas');
    container.appendChild(canvas);
    const gl = canvas.getContext('webgl2') || canvas.getContext('webgl');

    if (!gl) {
        console.error("WebGL tidak didukung");
        return;
    }

    const vs = `#version 300 es
    in vec2 position;
    void main() { gl_Position = vec4(position, 0.0, 1.0); }`;

    const fs = `#version 300 es
    precision highp float;
    uniform vec2 iResolution;
    uniform float iTime;
    uniform vec3 uColor1;
    uniform vec3 uColor2;
    uniform vec3 uColor3;
    out vec4 fragColor;
    
    mat2 Rot(float a){float s=sin(a),c=cos(a);return mat2(c,-s,s,c);} 
    float hash(vec2 p){ return fract(sin(dot(p,vec2(12.9898,78.233)))*43758.5453); }
    
    void main() {
        vec2 uv = gl_FragCoord.xy / iResolution.xy;
        float t = iTime * 0.8;
        
        // Simuasi Warp Sederhana
        vec2 p = uv - 0.5;
        p *= Rot(t * 0.1);
        p.x += sin(p.y * 5.0 + t) * 0.2;
        p.y += cos(p.x * 5.0 + t) * 0.2;
        
        float mask = smoothstep(-0.5, 0.5, p.x + p.y);
        vec3 col = mix(uColor3, mix(uColor2, uColor1, mask), uv.y);
        
        // Grain
        float grain = (hash(gl_FragCoord.xy + iTime) - 0.5) * 0.04;
        col += grain;
        
        fragColor = vec4(clamp(col, 0.0, 1.0), 1.0);
    }
    `;

    function createShader(gl, type, source) {
        const shader = gl.createShader(type);
        gl.shaderSource(shader, source);
        gl.compileShader(shader);
        return shader;
    }

    const program = gl.createProgram();
    gl.attachShader(program, createShader(gl, gl.VERTEX_SHADER, vs));
    gl.attachShader(program, createShader(gl, gl.FRAGMENT_SHADER, fs));
    gl.linkProgram(program);
    gl.useProgram(program);

    const vertices = new Float32Array([-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1]);
    const buffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
    gl.bufferData(gl.ARRAY_BUFFER, vertices, gl.STATIC_DRAW);

    const posLoc = gl.getAttribLocation(program, "position");
    gl.enableVertexAttribArray(posLoc);
    gl.vertexAttribPointer(posLoc, 2, gl.FLOAT, false, 0, 0);

    const resLoc = gl.getUniformLocation(program, "iResolution");
    const timeLoc = gl.getUniformLocation(program, "iTime");
    const c1Loc = gl.getUniformLocation(program, "uColor1");
    const c2Loc = gl.getUniformLocation(program, "uColor2");
    const c3Loc = gl.getUniformLocation(program, "uColor3");

    function hexToRgb(hex) {
        const r = parseInt(hex.slice(1, 3), 16) / 255;
        const g = parseInt(hex.slice(3, 5), 16) / 255;
        const b = parseInt(hex.slice(5, 7), 16) / 255;
        return [r, g, b];
    }

    function render(time) {
        canvas.width = container.clientWidth;
        canvas.height = container.clientHeight;
        gl.viewport(0, 0, canvas.width, canvas.height);

        gl.uniform2f(resLoc, canvas.width, canvas.height);
        gl.uniform1f(timeLoc, time * 0.001);
        gl.uniform3fv(c1Loc, hexToRgb('#0b1727'));
        gl.uniform3fv(c2Loc, hexToRgb('#0d8276'));
        gl.uniform3fv(c3Loc, hexToRgb('#111827'));

        gl.drawArrays(gl.TRIANGLES, 0, 6);
        requestAnimationFrame(render);
    }
    requestAnimationFrame(render);
});